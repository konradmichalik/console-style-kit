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

use ConsoleStyleKit\Enums\BadgeColor;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * BadgeElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class BadgeElement extends AbstractStyleElement
{
    private string $text;
    private BadgeColor $color = BadgeColor::GRAY;

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setColor(BadgeColor|string $color): self
    {
        $this->color = $color instanceof BadgeColor ? $color : BadgeColor::from($color);

        return $this;
    }

    public static function create(SymfonyStyle $style, string $text, BadgeColor|string $color = BadgeColor::GRAY, bool $verboseOnly = false): self
    {
        return (new self($style, $verboseOnly))
            ->setText($text)
            ->setColor($color);
    }

    public static function success(SymfonyStyle $style, string $text): self
    {
        return self::create($style, $text, BadgeColor::GREEN);
    }

    public static function info(SymfonyStyle $style, string $text): self
    {
        return self::create($style, $text, BadgeColor::BLUE);
    }

    public static function warning(SymfonyStyle $style, string $text): self
    {
        return self::create($style, $text, BadgeColor::YELLOW);
    }

    public static function error(SymfonyStyle $style, string $text): self
    {
        return self::create($style, $text, BadgeColor::RED);
    }

    public function __toString(): string
    {
        return "<bg={$this->color->value};fg=white> {$this->text} </>";
    }
}
