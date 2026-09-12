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

use ConsoleStyleKit\Contracts\StyleElementInterface;
use Stringable;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * AbstractStyleElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
abstract class AbstractStyleElement implements StyleElementInterface, Stringable
{
    public function __construct(protected SymfonyStyle $style, protected bool $verboseOnly = false) {}

    protected function getTerminalWidth(): int
    {
        if (function_exists('exec')) {
            $output = [];
            exec('tput cols 2>/dev/null', $output);
            if (!empty($output[0]) && is_numeric($output[0])) {
                return (int) $output[0];
            }
        }

        return 80; // fallback
    }

    public function render(): void
    {
        if ($this->verboseOnly && !$this->style->isVerbose()) {
            return;
        }
        $this->style->writeln($this->__toString());
    }
}
