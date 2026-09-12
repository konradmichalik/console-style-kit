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

namespace ConsoleStyleKit\Tests\Unit;

use ConsoleStyleKit\Elements\SeparatorElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * SeparatorElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class SeparatorElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testDefaultSeparator(): void
    {
        $element = new SeparatorElement($this->style);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('-', $output);
    }

    public function testCustomCharacter(): void
    {
        $element = new SeparatorElement($this->style);
        $element->setCharacter('=')->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('=', $output);
    }

    public function testCustomWidth(): void
    {
        $element = new SeparatorElement($this->style);
        $element->setWidth(10)->render();

        $output = $this->output->fetch();
        $lines = explode("\n", trim($output));
        $this->assertSame(10, strlen(trim($lines[0])));
    }

    public function testFluentInterface(): void
    {
        $element = new SeparatorElement($this->style);

        $result = $element->setCharacter('*');
        $this->assertSame($element, $result);

        $result = $element->setWidth(5);
        $this->assertSame($element, $result);
    }

    public function testStaticMethods(): void
    {
        $methods = [
            'line' => '-',
            'dots' => '.',
            'equals' => '=',
        ];

        foreach ($methods as $method => $expectedChar) {
            $this->output = new BufferedOutput();
            $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);

            $element = SeparatorElement::{$method}($this->style, 5);
            $element->render();

            $output = $this->output->fetch();
            $this->assertStringContainsString($expectedChar, $output);
        }
    }

    public function testStaticCreate(): void
    {
        $element = SeparatorElement::create($this->style);
        $this->assertInstanceOf(SeparatorElement::class, $element);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('-', $output);
    }
}
