<?php

declare (strict_types = 1);

namespace Levi\LaravelRotateCaptcha\Support;

use Levi\LaravelRotateCaptcha\Captcha;

class File
{
	protected static ?object $instance = null;

	public array $extensions = ['webp', 'png', 'jpg', 'jpeg'];
	public array $files = [];

	public ?string $directory = null;

	protected ?object $items = null;

	public static function make(string $directory): self
	{
		if (is_null(static::$instance))
        {
			return static::$instance = new static($directory);
		}

		return static::$instance;
	}

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

    public function clear(int $time = 86400, bool $ext = false): self
    {
        $disk  = Captcha::disk();
        $now   = now()->timestamp;

        $items = $this->list($ext);
        $items = array_filter(
            $items, fn (string $name) => $disk->lastModified($name) + $time < $now
        );

        $items !== [] && $disk->delete($items);
        return $this;
    }

    public function cost(bool $ext = false): int
    {
        return count($this->list($ext));
    }

	/**
	 * Increase the available suffix
	 *
	 * @param array $extensions
	 * @return int
	 */
	public function extension(array $extensions): self
	{
		$this->extensions = array_merge($this->extensions, $extensions);
		return $this;
	}

    public function list(bool $ext = false): array
    {
        $items = Captcha::disk()->files($this->directory);
        return !$ext ? $items : array_filter($items, function ($item) {
            $path = explode('.', $item);
            return in_array(array_pop($path), $this->extensions);
        });
    }

    public function prepend(string $name, string $context): bool
    {
        $path = $this->directory. DIRECTORY_SEPARATOR. $name;
        return Captcha::disk()->prepend($path, $context);
    }

	/**
	 * Randomly extract files
	 *
	 * @param int $limit
	 * @return string|array
	 */
	public function rand(int $limit = 1): string|array
	{
        $limit = max(1, $limit);
		if (empty($this->files) || !count($this->files))
        {
			$this->files = $this->list(true);
		}

        if ([] === $this->files)
        {
            return $limit > 1 ? [] : '';
        }

		shuffle($this->files);
		if ($limit > 1)
        {
			return array_intersect_key(
                $this->files, array_flip((array)array_rand($this->files, $limit))
            );
		}

		$rand = mt_rand(0, count($this->files) - 1);
		return $this->files[$rand];
	}

	/**
	 * Remove all files in the directory
	 *
	 * @return bool
	 */
	public function remove(): bool
	{
        $protect = [
            config('rotate.captcha.storePath') . DIRECTORY_SEPARATOR . 'origin',
            config('rotate.captcha.storePath') . DIRECTORY_SEPARATOR . 'transform',
        ];

        $action = Captcha::disk()->deleteDirectory($this->directory);
        if (!$action) return $action;

        if (in_array($this->directory, $protect))
        {
            Captcha::disk()->makeDirectory($this->directory);
        }
        return true;
	}
}
