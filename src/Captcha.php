<?php

declare(strict_types = 1);

namespace Levi\LaravelRotateCaptcha;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Levi\LaravelRotateCaptcha\Handle\GdHandle;
use Levi\LaravelRotateCaptcha\Handle\ImagickHandle;
use Levi\LaravelRotateCaptcha\Support\File;
use Levi\LaravelRotateCaptcha\Support\Store;

class Captcha
{
    const OUTPUT_JPG = 'jpg';
    const OUTPUT_PNG = 'png';
    const OUTPUT_WEBP = 'webp';

    const OUTPUT_TYPE = [
        self::OUTPUT_JPG  => 'image/jpeg',
        self::OUTPUT_PNG  => 'image/png',
        self::OUTPUT_WEBP => 'image/webp',
    ];

    const TICKET_RULE = [
        'num'     => 1,
        'time'    => 300,
        'routers' => null,
    ];

    private static string $_cache = '';

    private static string $_disk = '';

    /**
     * Whether the same angle of the image has been generated
     */
    private bool $_existed = false;

    /**
     * Image handle class
     */
    private ?Handle $_handle = null;

    /**
     * The image path used to generate the rotate captcha image
     */
    private string $_image = '';

    /**
     * Image information
     */
    private array $_info = [];

    /**
     * Image ext
     */
    private string $_output = 'webp';

    public static function cache(): Repository
    {
        return Cache::store(self::$_cache);
    }

    public static function disk(): FilesystemAdapter
    {
        return Storage::disk(self::$_disk);
    }

    public static function lang(string $key, array $replace = []): string
    {
        if ($key === '') return '';

        $local = config('rotate.captcha.lang', app('app')->currentLocale());
        return trans(
            sprintf('rotate.captcha::lang.%s', $key), $replace, $local
        );
    }

    public function __construct()
    {
        self::$_cache = config('rotate.captcha.cache') ?: config('cache.default');
        self::$_disk = config('rotate.captcha.disk') ?: config('filesystems.default');
    }

    /**
     * Build the necessary parameters
     */
    public function buildBase(int $size, ?string $output = null): array
    {
        $this->_setOutput($output);
        $size = max($size, 160);

        // Get random angle, generate angle hash
        $degrees = mt_rand(30, 270);
        $handle  = $this->_handleRaw();

        $cache_path      = $this->_getStoreFilePath($degrees, $size);
        $cache_file_path = $this->_transform(true) . DIRECTORY_SEPARATOR. $cache_path;

        $handle->setCachePathAndDegrees($cache_file_path, $degrees);
        return [
            $handle,
            [
                'angle'  => $degrees,
                'cache'  => $cache_file_path,
                'output' => $output,
                'path'   => $cache_path,
                'size'   => $size,
            ]
        ];
    }

    public function create(
        string $ua, string $ip, int $size = 350, ?string $output = null): self
    {
        if (!extension_loaded('gd') && !extension_loaded('imagick'))
        {
            throw new CaptchaException(
                self::lang('Need to support GD or Imagick extension.')
            );
        }

        $path = config('rotate.captcha.storePath') . DIRECTORY_SEPARATOR . 'origin';
        $this->_image = File::make($path)->rand();

        if ('' === $this->_image)
        {
            throw new CaptchaException(
                self::lang('Please pass in the material image.')
            );
        }

        // Create image handle class
        // Build the necessary parameters
        [$handle, $base] = $this->buildBase($size, $output);

        // Image from the same angle
        if (is_file($base['cache']))
        {
            $this->_existed = true;
            $info = $handle->getInfo($base['cache']);
        }
        else
        {
            // Get image information
            $info = $handle->getInfo();
            if (is_null($handle->front) && !$handle->createFront())
            {
                throw new CaptchaException(
                    self::lang('Failed to create image.')
                );
            }
        }

        $this->_info = array_merge($info, $base, [
            'token' => $this->_buildToken($ua, $ip, $base['angle']),
        ]);
        return $this;
    }

    /**
     * Generate verification pictures and obtain relevant information
     */
    public function get(): array
    {
        // 拿不到信息也不用生成了
        if ([] === ($info = $this->info())) return $info;
        if (!$this->_existed)
        {
            // save image and set global size
            $this->_existed = $this->_handleRaw()->save($this->_info['size']);
        }

        return $this->_existed ? $info : [];
    }

    /**
     * Get output mime
     */
    public function getMime(): string
    {
        return self::OUTPUT_TYPE[$this->_output];
    }

    /**
     * Get information about the generated image
     */
    public function info(): array
    {
        $token = $this->_info['token'] ?? '';
        return $token === '' ? [] : [
            'str'   => Crypt::encryptString($this->_info['path'] ?? ''),
            'token' => $token,
        ];
    }

    /**
     * According to the encrypted string, get the image content data
     */
    public function output(string $str): array
    {
        try
        {
            if (empty($str)) return [null, ''];
            $str = Crypt::decryptString($str);
        }
        catch (DecryptException $e)
        {
            return [null, ''];
        }

        $filepath = $this->_transform() . DIRECTORY_SEPARATOR . $str;
        if (is_null($filepath)) return [null, ''];

        $mime = 'image/'. pathinfo($filepath, PATHINFO_EXTENSION);
        return [$mime, file_get_contents($filepath)];
    }

    /**
     * get ticket info
     */
    public function replaceTicket(string $token, string $policie, array $params): array
    {
        $policie = $policie ?: config('rotate.captcha.policie');
        $store   = new Store;

        do
        {
            $sid = Str::random(32);
        }
        while($store->hasKey($sid, true));

        // 避免config配置错误，这里加上默认配置
        $rule = (config('rotate.captcha.rules', [])[$policie] ?? []) + self::TICKET_RULE;
        $info = $store->forget($token)->ticket($sid, $policie, 0, $rule['time']);

        return $info === null ? [] : [
            'sid'    => $sid,
            'ticket' => password_hash($sid, PASSWORD_DEFAULT, $params),
        ];
    }

    public function verifyTicket(string $sid, string $ticket, string $route_name = ''): bool
    {
        $store  = new Store;
        $result = password_verify($sid, $ticket) ? $store->ticket($sid) : null;
        if ($result === null) return false;

        $policie = $result['policie'];
        $expire  = $result['expire'];
        $uesd    = $result['used'];

        $rule = (config('rotate.captcha.rules', [])[$policie] ?? []) + self::TICKET_RULE;
        if ($rule['num'] < 0 || $rule['time'] < 0) return false;


        try
        {
            // 要先++
            $fail = $rule['num'] < $uesd++;
            $now  = now()->timestamp;

            if ($rule['num'] > 0 && ($fail || $rule['num'] === $uesd))
            {
                throw new CaptchaException('num', $fail ? 1 : 0);
            }

            if ($rule['time'] > 0 && $now >= $expire)
            {
                throw new CaptchaException('time', $now === $expire ? 0 : 1);
            }

            $store->ticket($sid, $policie, $uesd, $expire - $now);
            $routers = $rule['routers'];
        }
        catch (CaptchaException $e)
        {
            $store->forget($sid, true);
            $routers = $e->getCode() === 0 ? $rule['routers'] : [];
        }

        return $routers === null ? true : in_array($route_name, $routers);
    }

    private function _buildToken(string $ua, string $ip, int $degrees): string
    {
        $expire = config('rotate.captcha.expire', 300);
        $limit  = config('rotate.captcha.limit', 2);

        $store_info = [
            'ds' => $degrees,
            'ip' => $ip,
            'ua' => crc32($ua)
        ];

        return (new Store)->put($store_info, $limit, $expire);
    }

    private function _createTransform(array $path): void
    {
        $target = implode(DIRECTORY_SEPARATOR, $path);
        $disk   = self::disk();

        if (!$disk->exists($target)) $disk->makeDirectory($target);
    }

    /**
     * 路径规则：原图绝对路径?angle=角度&size=尺寸.格式
     *  - 规则一样就会使用已有的图片
     */
    private function _getStoreFilePath(int $degrees, int $size): string
    {
        // _token=%s&hash=%s
        $link = sprintf(
            '%s?angle=%s&size=%s',
            $this->_image, sprintf("%03d", $degrees), $size,
        );
        return md5($link) . $this->_handleRaw()->getFileExt($this->_image, false);
    }

    private function _handleRaw(): Handle
    {
        if (!is_null($this->_handle)) return $this->_handle;

        $path   = storage_path('app' . DIRECTORY_SEPARATOR . $this->_image);
        $config = config('rotate.captcha.img');

        if ($this->_handleName() === 'imagick')
        {
            $this->_handle = new ImagickHandle($this, $path, $config);
        }
        else
        {
            $this->_handle = new GdHandle($this, $path, $config);
        }

        return $this->_handle;
    }

    private function _handleName(): string
    {
        if (config('rotate.captcha.handle') === 'imagick' && extension_loaded('imagick'))
        {
            return 'imagick';
        }
        return 'gd';
    }

    /**
     * 原则上是按照配置文件，为了考虑到兼容性提供一个口子
     */
    private function _setOutput(?string $output = null)
    {
        if (!array_key_exists($output, self::OUTPUT_TYPE))
        {
            $output = config('rotate.captcha.outputType', 'webp');
            if (!array_key_exists($output, self::OUTPUT_TYPE))
            {
                $output = 'webp';
            }
        }

        $this->_output = $output;
    }

    /**
     * Get transform dir
     */
    private function _transform(bool $create = false): string
    {
        $dir  = config('rotate.captcha.storePath', 'rotate.captcha');
        $path = ['app', $dir, 'transform'];

        if ($create)
        {
            $this->_createTransform(array_slice($path, 1));
        }
        return storage_path(implode(DIRECTORY_SEPARATOR, $path));
    }
}
