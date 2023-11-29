<?php

declare (strict_types = 1);

namespace Levi\LaravelRotateCaptcha\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Levi\LaravelRotateCaptcha\Captcha;

class Store
{
    const TOKEN_PRE = 'rotate_captcha_';
    const TOKEN_TICKET = 'rotate_ticket_';

    public function __construct() {}

    public function buildPlayload(array $params): array
    {
        $payload = json_encode($params, JSON_UNESCAPED_UNICODE);
        $token = Str::random(32);

        return [$token, Crypt::encryptString($payload)];
    }

    /**
     * Forget payload
     */
    public function forget(string $token, bool $ticket = false): self
    {
        $prefix = $ticket ? self::TOKEN_TICKET : self::TOKEN_PRE;
        Captcha::cache()->forget($prefix . $token);

        return $this;
    }

    /**
     * Get payload
     */
    public function get(string $token, $consume = true): array
    {
        $cache = Captcha::cache();
        $key   = self::TOKEN_PRE . $token;

        if (!$cache->has($key)) return [];

        $payload = $cache->get($key);
        if (empty($payload)) return [];

        $info = json_decode(Crypt::decryptString($payload), true);
        $this->update($token, $info, $consume);

        return $info;
    }

    public function hasKey(string $token, bool $ticket = false): bool
    {
        $prefix = $ticket ? self::TOKEN_TICKET : self::TOKEN_PRE;
        return Captcha::cache()->has($prefix . $token);
    }

    /**
     * Storage token
     * $info = [int $degrees, string $ua, string $ip];
     */
    public function put(array $info, int $limit = 2, int $ttl = 30): string
    {
        $info = array_merge($info, [
            'limit' => $limit,
            'ttl'   => now()->timestamp + $ttl,
        ]);

        [$token, $payload] = $this->buildPlayload($info);
        Captcha::cache()->put(self::TOKEN_PRE . $token, $payload, $ttl);

        return $token;
    }

    /**
     * Use ticket
     * ---
     * policie rule:
     *  - `null`: 获取sid策略
     *  - `string`: 存储策略，空字符采用默认策略（没有采用默认定义）
     * ---
     * expire rule
     *  - `> 0`: 设置存储有效期
     *  - `= 0`: 无限期存储
     *  - `< 0`: 不存储
     * ---
     * return rule:
     *  - `null`: 查找的策略不存在
     *  - `array`: 存储sid对应的策略，策略可以是空字符采用默认策略（没有采用默认定义）
     */
    public function ticket(
        string $sid, ?string $policie = null, int $used = 0, int $expire = 300): ?array
    {
        $cache = Captcha::cache();
        $key   = self::TOKEN_TICKET . $sid;

        if (null === $policie)
        {
            // 找到就返回，不要在store处理过期
            return !$cache->has($key) ? null : json_decode($cache->get($key), true);
        }

        if ($expire < 0) return null;
        $ttl  = $expire > 0;
        $data = [
            'policie' => $policie,
            'used'    => $used,
            'expire'  => $ttl ? now()->timestamp + $expire : 0
        ];

        $cache->put($key, json_encode($data, JSON_FORCE_OBJECT), $ttl ? $expire : null);
        return $data;
    }

    /**
     * Update payload
     *  - 检查过期或更新数据
     *  - $consume为`false`时，没过期的数据什么都不做
     */
    public function update(string $token, array $info, bool $consume): self
    {
        $limit = $info['limit'] ?? 0;
        $ttl   = $info['ttl'] ?? 0;

        if ($consume) $limit--;
        if ($limit <= 0 || $ttl <= now()->timestamp)
        {
            $this->forget($token);
            return $this;
        }

        if ($consume)
        {
            $info['limit'] = $limit;
            Captcha::cache()->put($token, $info, $ttl - now()->timestamp);
        }
        return $this;
    }
}
