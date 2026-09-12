<?php

/*
 * This file is part of the Composer plugin "console-style-kit".
 *
 * Copyright (C) 2025-2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace ConsoleStyleKit\Elements;

use ConsoleStyleKit\Enums\RatingStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * RatingElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class RatingElement extends AbstractStyleElement
{
    private int $max;
    private int $current;
    private RatingStyle $displayStyle = RatingStyle::CIRCLE;
    private bool $colorful = false;

    public function setRating(int $max, int $current): self
    {
        $this->max = $max;
        $this->current = $current;

        return $this;
    }

    public function setStyle(RatingStyle|string $style): self
    {
        $this->displayStyle = $style instanceof RatingStyle ? $style : RatingStyle::from($style);

        return $this;
    }

    public function setColorful(bool $colorful): self
    {
        $this->colorful = $colorful;

        return $this;
    }

    public function __toString(): string
    {
        return match ($this->displayStyle) {
            RatingStyle::CIRCLE => $this->generateCircle(),
            RatingStyle::BAR => $this->generateBar(),
        };
    }

    private function generateCircle(): string
    {
        $circles = [];

        for ($i = 0; $i < $this->max; ++$i) {
            if ($i < $this->current) {
                if ($this->colorful) {
                    $color = $this->getRatingColor($this->current, $this->max);
                    $circles[] = "<fg={$color}>●</>";
                } else {
                    $circles[] = '<fg=white>●</>';
                }
            } else {
                $circles[] = '<fg=gray>○</>';
            }
        }

        return implode(' ', $circles);
    }

    private function generateBar(): string
    {
        $filled = str_repeat('#', $this->current);
        $empty = str_repeat('-', $this->max - $this->current);

        if ($this->colorful) {
            $color = $this->getRatingColor($this->current, $this->max);

            return "[<fg={$color}>{$filled}</><fg=gray>{$empty}</>]";
        }

        return "[<fg=white>{$filled}</><fg=gray>{$empty}</>]";
    }

    private function getRatingColor(int $current, int $max): string
    {
        $percentage = $current / $max;

        if ($percentage >= 0.8) {
            return 'green';
        }

        if ($percentage >= 0.5) {
            return 'yellow';
        }

        return 'red';
    }

    public static function create(SymfonyStyle $style, int $max, int $current, bool $verboseOnly = false): self
    {
        return (new self($style, $verboseOnly))->setRating($max, $current);
    }

    public static function circle(SymfonyStyle $style, int $max, int $current, bool $colorful = false, bool $verboseOnly = false): self
    {
        return self::create($style, $max, $current, $verboseOnly)
            ->setStyle(RatingStyle::CIRCLE)
            ->setColorful($colorful);
    }

    public static function bar(SymfonyStyle $style, int $max, int $current, bool $colorful = false, bool $verboseOnly = false): self
    {
        return self::create($style, $max, $current, $verboseOnly)
            ->setStyle(RatingStyle::BAR)
            ->setColorful($colorful);
    }
}
