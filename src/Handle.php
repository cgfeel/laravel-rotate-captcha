<?php

declare (strict_types = 1);

namespace Levi\LaravelRotateCaptcha;

use Levi\LaravelRotateCaptcha\Interface\HandleInterface;

abstract class Handle implements HandleInterface
{
	public $back = null;
    public ?string $cacheFilePath = null;
    public array $config = [];
	public $degrees = 0;
	public $front = null;
	public $image = '';
	public $info = [];
	public $size = 350;

    protected string $outputMime = 'image/webp';
    protected string $outputType = 'webp';

	public function calcSize(int $size = 350): ?array
	{
		if(!$this->info || is_null($this->front))
        {
			return null;
		}

		$this->size = $size ?? 350;

		// Minimum size of original image
		$src_min = min($this->info['width'], $this->info['height']);
		if($src_min < 160)
        {
			throw new CaptchaException(
                Captcha::lang('The image height and width dimensions must be greater than 160px.')
            );
		}

		if($src_min < $this->size)
        {
			$this->size = $src_min;
		}

		$src_w = $this->info['width'];
		$src_h = $this->info['height'];

		$dst_w = $dst_h = $this->size;

		$dst_scale = $dst_h / $dst_w; // Target image ratio
		$src_scale = $src_h / $src_w; // Original image aspect ratio

        // Too high
		if ($src_scale >= $dst_scale)
        {
			$w = intval($src_w);
			$h = $w;
			$x = 0;
			$y = (int) round(($src_h - $h) / 2);
		}
        else
        {
			$h = intval($src_h);
			$w = $h;
			$x = (int) round(($src_w - $w) / 2);
			$y = 0;
		}

		return [$src_w, $src_h, $dst_w, $dst_h, $dst_scale, $src_scale, $w, $h, $x, $y];
	}

	/**
	 * Get file output extension for mime
	 */
	public function getExt(): string
	{
        $type = array_flip(Captcha::OUTPUT_TYPE);
        return $type[$this->outputMime];
	}

	/**
	 * Get file output extension
	 */
	public function getFileExt(?string $filePath = null, bool $isIgnoreAfter = true): string
	{
		// $ext = strtolower(strrchr((is_null($filePath) ? $this->image : $filePath), '.'));
		$ext = pathinfo($filePath ?? $this->image, PATHINFO_EXTENSION);

		// Return original image suffix
		if($isIgnoreAfter)
        {
			return '.'. $ext;
		}

		// Output extension
		if('webp' === ($ext = $this->getExt()))
        {
			return '.'. $ext;
		}

		if(!empty($this->config['bgcolor']) && ($ext !== 'jpg' || $ext !== 'jpeg'))
        {
			$ext = 'jpg';
		}

		return '.'. $ext;
	}

	/**
	 * Hexadecimal color to RGB
	 *
	 * @param string $color
	 * @param bool $isReturnString
	 * @return string|array
	 */
	public function hex2rgb(string $color, bool $isReturnString = true): string|array|null
	{
		$hexColor = str_replace('#', '', $color);
		$lens = strlen($hexColor);

		if ($lens != 3 && $lens != 6)
        {
			return null;
		}

		$newColor = '';
		if ($lens == 3)
        {
			for ($i = 0; $i < $lens; $i++)
            {
				$newColor .= $hexColor[$i] . $hexColor[$i];
			}
		} else
        {
			$newColor = $hexColor;
		}

		$rgb = [];
		$hex = str_split($newColor, 2);

		foreach ($hex as $vls)
        {
			$rgb[] = hexdec($vls);
		}

		if($isReturnString)
        {
			return implode(', ', $rgb);
		}

		return $rgb;
	}

	/**
	 * Set the processed image path and rotation angle to the handle class
	 */
	public function setCachePathAndDegrees(?string $cacheFilePath = null, int $degrees = 0): bool
	{
		if (empty($cacheFilePath))
        {
			throw new CaptchaException(
                Captcha::lang('The path of the cached image cannot be empty.')
            );
		}

		$this->degrees = max($degrees, 30);
		$this->cacheFilePath = $cacheFilePath;

		return true;
	}
}
