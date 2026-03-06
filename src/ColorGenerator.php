<?php

namespace Kartovitskii\Laracolor;

use Kartovitskii\Laracolor\Contracts\ColorGeneratorInterface;
use Kartovitskii\Laracolor\Enums\ColorPalette;

class ColorGenerator implements ColorGeneratorInterface
{
    private array $config;
    private array $saturationRange;
    private array $lightnessRange;
    private ?int $hueOffset = null;
    private ?array $hueRange = null;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->initializeFromConfig();
    }

    private function initializeFromConfig(): void
    {
        $palette = $this->config['default_palette'] ?? ColorPalette::PASTEL->value;
        $this->setPalette($palette);
    }

    public function fromString(string $string): string
    {
        $string = $this->normalizeString($string);
        $hash = $this->generateHash($string);

        [$h, $s, $l] = $this->calculateHsl($hash);

        return $this->hslToHex($h, $s, $l);
    }

    public function toRgb(string $string): array
    {
        $hex = $this->fromString($string);
        return $this->hexToRgb($hex);
    }

    public function toHsl(string $string): array
    {
        $hash = $this->generateHash($this->normalizeString($string));
        [$h, $s, $l] = $this->calculateHsl($hash);

        return [
            'hue' => round($h, 2),
            'saturation' => round($s, 2),
            'lightness' => round($l, 2),
        ];
    }

    public function setPalette(string $palette): self
    {
        $paletteEnum = ColorPalette::tryFrom($palette) ?? ColorPalette::PASTEL;
        $ranges = $paletteEnum->ranges();

        $this->saturationRange = $ranges['s'] ?? [35, 56];
        $this->lightnessRange = $ranges['l'] ?? [78, 89];
        $this->hueRange = $ranges['hue_range'] ?? null;

        return $this;
    }

    public function setSaturationRange(int $min, int $max): self
    {
        $this->saturationRange = [$min, $max];
        return $this;
    }

    public function setLightnessRange(int $min, int $max): self
    {
        $this->lightnessRange = [$min, $max];
        return $this;
    }

    public function setHueOffset(int $offset): self
    {
        $this->hueOffset = $offset;
        return $this;
    }

    private function normalizeString(string $string): string
    {
        return mb_strtolower(trim($string), 'UTF-8');
    }

    private function generateHash(string $string): int
    {
        return hexdec(substr(sha1($string), 0, 8));
    }

    private function calculateHsl(int $hash): array
    {
        $hueBase = $hash % 360;

        if ($this->hueRange) {
            [$minHue, $maxHue] = $this->hueRange;
            $hueBase = $minHue + ($hueBase % ($maxHue - $minHue));
        }

        if ($this->hueOffset !== null) {
            $hueBase = ($hueBase + $this->hueOffset) % 360;
        }

        [$sMin, $sMax] = $this->saturationRange;
        $saturation = $sMin + (($hash >> 8) % ($sMax - $sMin + 1));

        [$lMin, $lMax] = $this->lightnessRange;
        $lightness = $lMin + (($hash >> 16) % ($lMax - $lMin + 1));

        return [$hueBase, $saturation, $lightness];
    }

    private function hslToHex(float $h, float $s, float $l): string
    {
        $h = fmod($h, 360.0);
        if ($h < 0) $h += 360.0;

        $h /= 360.0;
        $s /= 100.0;
        $l /= 100.0;

        if ($s == 0.0) {
            $v = (int) round($l * 255);
            return sprintf('#%02x%02x%02x', $v, $v, $v);
        }

        $q = ($l < 0.5) ? ($l * (1 + $s)) : ($l + $s - $l * $s);
        $p = 2 * $l - $q;

        $r = $this->hueToRgb($p, $q, $h + 1/3);
        $g = $this->hueToRgb($p, $q, $h);
        $b = $this->hueToRgb($p, $q, $h - 1/3);

        return sprintf(
            '#%02x%02x%02x',
            (int) round($r * 255),
            (int) round($g * 255),
            (int) round($b * 255)
        );
    }

    private function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;

        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'red' => hexdec(substr($hex, 0, 2)),
            'green' => hexdec(substr($hex, 2, 2)),
            'blue' => hexdec(substr($hex, 4, 2)),
        ];
    }

    public function getContrastColor(string $string): string
    {
        $rgb = $this->toRgb($string);
        $brightness = ($rgb['red'] * 299 + $rgb['green'] * 587 + $rgb['blue'] * 114) / 1000;

        return $brightness > 128 ? '#000000' : '#ffffff';
    }

    public function getGradient(string $string, int $steps = 5): array
    {
        $colors = [];
        $baseColor = $this->fromString($string);
        $rgb = $this->hexToRgb($baseColor);

        for ($i = 0; $i < $steps; $i++) {
            $factor = $i / ($steps - 1);
            $colors[] = $this->adjustBrightness($rgb, $factor);
        }

        return $colors;
    }

    private function adjustBrightness(array $rgb, float $factor): string
    {
        $r = (int) round($rgb['red'] * (0.5 + $factor * 0.5));
        $g = (int) round($rgb['green'] * (0.5 + $factor * 0.5));
        $b = (int) round($rgb['blue'] * (0.5 + $factor * 0.5));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}