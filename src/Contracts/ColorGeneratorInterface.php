<?php

namespace Kartovitskii\Laracolor\Contracts;

interface ColorGeneratorInterface
{
    public function fromString(string $string): string;
    public function setPalette(string $palette): self;
    public function setSaturationRange(int $min, int $max): self;
    public function setLightnessRange(int $min, int $max): self;
    public function setHueOffset(int $offset): self;
    public function toRgb(string $string): array;
    public function toHsl(string $string): array;
}