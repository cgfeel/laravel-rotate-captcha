<?php

namespace Levi\LaravelRotateCaptcha\Interface;

interface HandleInterface
{
    public function getInfo(string $filePath = null): array;
    public function save(int $size = 350): ?bool;
    public function build(int $size = 350): ?bool;
    public function createFront(): ?bool;
}
