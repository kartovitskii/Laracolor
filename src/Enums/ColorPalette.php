<?php

namespace Kartovitskii\Laracolor\Enums;

enum ColorPalette: string
{
    case PASTEL = 'pastel';
    case VIBRANT = 'vibrant';
    case DARK = 'dark';
    case LIGHT = 'light';
    case MUTED = 'muted';
    case WARM = 'warm';
    case COLD = 'cold';
    case RANDOM = 'random';

    public function ranges(): array
    {
        return match($this) {
            self::PASTEL => ['s' => [20, 40], 'l' => [70, 85]],
            self::VIBRANT => ['s' => [70, 100], 'l' => [45, 65]],
            self::DARK => ['s' => [50, 90], 'l' => [15, 35]],
            self::LIGHT => ['s' => [20, 50], 'l' => [80, 95]],
            self::MUTED => ['s' => [30, 50], 'l' => [40, 60]],
            self::WARM => ['s' => [50, 80], 'l' => [45, 70], 'hue_range' => [0, 60]],
            self::COLD => ['s' => [50, 80], 'l' => [45, 70], 'hue_range' => [180, 260]],
            self::RANDOM => ['s' => [0, 100], 'l' => [0, 100]],
        };
    }
}