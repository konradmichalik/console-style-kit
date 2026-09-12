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

use ConsoleStyleKit\Elements\BlockquoteElement;
use ConsoleStyleKit\Enums\BlockquoteType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * BlockquoteElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class BlockquoteElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testSimpleBlockquote(): void
    {
        $element = new BlockquoteElement($this->style);
        $element->setText('Test blockquote')->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Test blockquote', $output);
        $this->assertStringContainsString('│', $output);
        $this->assertStringContainsString('╷', $output); // top border
        $this->assertStringContainsString('╵', $output); // bottom border
    }

    public function testBlockquoteWithType(): void
    {
        $element = new BlockquoteElement($this->style);
        $element->setText('Info message')->setType(BlockquoteType::INFO)->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Info message', $output);
        $this->assertStringContainsString('INFO', $output);
        $this->assertStringContainsString('╷', $output); // top border
        $this->assertStringContainsString('╵', $output); // bottom border
    }

    public function testFluentInterface(): void
    {
        $element = new BlockquoteElement($this->style);
        $result = $element->setText('Test');

        $this->assertSame($element, $result);

        $result = $element->setType(BlockquoteType::WARNING);
        $this->assertSame($element, $result);
    }

    public function testStaticCreate(): void
    {
        $element = BlockquoteElement::create($this->style, 'Static test');
        $this->assertInstanceOf(BlockquoteElement::class, $element);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('Static test', $output);
    }

    public function testStaticCreateWithType(): void
    {
        $element = BlockquoteElement::create($this->style, 'Typed test', BlockquoteType::CAUTION);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Typed test', $output);
        $this->assertStringContainsString('CAUTION', $output);
    }

    public function testConstants(): void
    {
        $this->assertEquals('INFO', BlockquoteType::INFO->value);
        $this->assertEquals('TIP', BlockquoteType::TIP->value);
        $this->assertEquals('IMPORTANT', BlockquoteType::IMPORTANT->value);
        $this->assertEquals('WARNING', BlockquoteType::WARNING->value);
        $this->assertEquals('CAUTION', BlockquoteType::CAUTION->value);
    }

    public function testAllBlockquoteTypes(): void
    {
        $types = [
            BlockquoteType::INFO,
            BlockquoteType::TIP,
            BlockquoteType::IMPORTANT,
            BlockquoteType::WARNING,
            BlockquoteType::CAUTION,
        ];

        foreach ($types as $type) {
            $this->output = new BufferedOutput();
            $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);

            $element = BlockquoteElement::create($this->style, "Test {$type->value}", $type);
            $element->render();

            $output = $this->output->fetch();
            $this->assertStringContainsString("Test {$type->value}", $output);
            $this->assertStringContainsString($type->value, $output);
        }
    }
}
