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

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * KeyValueElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class KeyValueElement extends AbstractStyleElement
{
    private string $key;
    private string $value;
    private ?string $keyColor = null;
    private string $separator = ': ';

    public function setKeyValue(string $key, string $value): self
    {
        $this->key = $key;
        $this->value = $value;

        return $this;
    }

    public function setKeyColor(?string $keyColor): self
    {
        $this->keyColor = $keyColor;

        return $this;
    }

    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    public function __toString(): string
    {
        if ($this->keyColor) {
            return "<fg={$this->keyColor};options=bold>{$this->key}</>{$this->separator}{$this->value}";
        }

        return "<options=bold>{$this->key}</>{$this->separator}{$this->value}";
    }

    public static function create(SymfonyStyle $style, string $key, string $value, ?string $keyColor = null, bool $verboseOnly = false): self
    {
        return (new self($style, $verboseOnly))
            ->setKeyValue($key, $value)
            ->setKeyColor($keyColor);
    }

    public static function withArrow(SymfonyStyle $style, string $key, string $value, ?string $keyColor = null): self
    {
        return self::create($style, $key, $value, $keyColor)
            ->setSeparator(' → ');
    }

    public static function withEquals(SymfonyStyle $style, string $key, string $value, ?string $keyColor = null): self
    {
        return self::create($style, $key, $value, $keyColor)
            ->setSeparator(' = ');
    }
}
