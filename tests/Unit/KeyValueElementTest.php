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

use ConsoleStyleKit\Elements\KeyValueElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * KeyValueElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class KeyValueElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testBasicKeyValue(): void
    {
        $element = new KeyValueElement($this->style);
        $element->setKeyValue('Name', 'John')->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('John', $output);
        $this->assertStringContainsString(': ', $output);
    }

    public function testKeyWithColor(): void
    {
        $element = new KeyValueElement($this->style);
        $element->setKeyValue('Status', 'Active')->setKeyColor('green')->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Status', $output);
        $this->assertStringContainsString('Active', $output);
    }

    public function testCustomSeparator(): void
    {
        $element = new KeyValueElement($this->style);
        $element->setKeyValue('Input', 'Output')->setSeparator(' → ')->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Input', $output);
        $this->assertStringContainsString('Output', $output);
        $this->assertStringContainsString('→', $output);
    }

    public function testFluentInterface(): void
    {
        $element = new KeyValueElement($this->style);

        $result = $element->setKeyValue('Key', 'Value');
        $this->assertSame($element, $result);

        $result = $element->setKeyColor('blue');
        $this->assertSame($element, $result);

        $result = $element->setSeparator(' = ');
        $this->assertSame($element, $result);
    }

    public function testStaticCreate(): void
    {
        $element = KeyValueElement::create($this->style, 'Version', '1.0.0');
        $this->assertInstanceOf(KeyValueElement::class, $element);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('Version', $output);
        $this->assertStringContainsString('1.0.0', $output);
    }

    public function testStaticWithArrow(): void
    {
        $element = KeyValueElement::withArrow($this->style, 'From', 'To');
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('From', $output);
        $this->assertStringContainsString('To', $output);
        $this->assertStringContainsString('→', $output);
    }

    public function testStaticWithEquals(): void
    {
        $element = KeyValueElement::withEquals($this->style, 'Variable', 'Value');
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('Variable', $output);
        $this->assertStringContainsString('Value', $output);
        $this->assertStringContainsString(' = ', $output);
    }
}
