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

use ConsoleStyleKit\Elements\BadgeElement;
use ConsoleStyleKit\Enums\BadgeColor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * BadgeElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class BadgeElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testSimpleBadge(): void
    {
        $element = new BadgeElement($this->style);
        $element->setText('TEST')->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('TEST', $output);
    }

    public function testBadgeWithColor(): void
    {
        $element = new BadgeElement($this->style);
        $element->setText('SUCCESS')->setColor(BadgeColor::GREEN)->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('SUCCESS', $output);
    }

    public function testFluentInterface(): void
    {
        $element = new BadgeElement($this->style);

        $result = $element->setText('Test');
        $this->assertSame($element, $result);

        $result = $element->setColor(BadgeColor::BLUE);
        $this->assertSame($element, $result);
    }

    public function testStaticCreate(): void
    {
        $element = BadgeElement::create($this->style, 'STATIC');
        $this->assertInstanceOf(BadgeElement::class, $element);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('STATIC', $output);
    }

    public function testSemanticMethods(): void
    {
        $methods = [
            'success' => 'SUCCESS',
            'info' => 'INFO',
            'warning' => 'WARNING',
            'error' => 'ERROR',
        ];

        foreach ($methods as $method => $text) {
            $this->output = new BufferedOutput();
            $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);

            $element = BadgeElement::{$method}($this->style, $text);
            $element->render();

            $output = $this->output->fetch();
            $this->assertStringContainsString($text, $output);
        }
    }

    public function testConstants(): void
    {
        $this->assertEquals('green', BadgeColor::GREEN->value);
        $this->assertEquals('blue', BadgeColor::BLUE->value);
        $this->assertEquals('yellow', BadgeColor::YELLOW->value);
        $this->assertEquals('red', BadgeColor::RED->value);
        $this->assertEquals('magenta', BadgeColor::MAGENTA->value);
        $this->assertEquals('gray', BadgeColor::GRAY->value);
    }
}
