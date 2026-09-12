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

namespace ConsoleStyleKit\Enums;

/**
 * BlockquoteType.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
enum BlockquoteType: string
{
    case INFO = 'INFO';
    case TIP = 'TIP';
    case IMPORTANT = 'IMPORTANT';
    case WARNING = 'WARNING';
    case CAUTION = 'CAUTION';

    public function getColor(): string
    {
        return match ($this) {
            self::INFO => 'blue',
            self::TIP => 'green',
            self::IMPORTANT => 'magenta',
            self::WARNING => 'yellow',
            self::CAUTION => 'red',
        };
    }

    public function isBold(): bool
    {
        return true;
    }
}
