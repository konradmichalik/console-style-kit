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

use ConsoleStyleKit\Elements\LoadingElement;
use ConsoleStyleKit\Enums\LoadingCharacterSet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * LoadingElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class LoadingElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testSimpleLoading(): void
    {
        $element = new LoadingElement($this->style);
        $element->setText('Testing')->render(1); // 1 second duration

        $output = $this->output->fetch();
        $this->assertStringContainsString('Testing', $output);
    }

    public function testFluentInterface(): void
    {
        $element = new LoadingElement($this->style);

        $result = $element->setText('Test');
        $this->assertSame($element, $result);

        $result = $element->hideDots();
        $this->assertSame($element, $result);

        $result = $element->showDots();
        $this->assertSame($element, $result);

        $result = $element->setColor('green');
        $this->assertSame($element, $result);

        $result = $element->setCharacterSet(LoadingCharacterSet::DOTS);
        $this->assertSame($element, $result);

        $result = $element->start();
        $this->assertSame($element, $result);

        $result = $element->stop();
        $this->assertSame($element, $result);
    }

    public function testStaticCreate(): void
    {
        $element = LoadingElement::create($this->style, 'Static Test');
        $this->assertInstanceOf(LoadingElement::class, $element);

        $element->render(1); // 1 second duration
        $output = $this->output->fetch();
        $this->assertStringContainsString('Static Test', $output);
    }

    public function testDefaultValues(): void
    {
        $element = LoadingElement::create($this->style);
        $element->render(1); // 1 second duration

        $output = $this->output->fetch();
        $this->assertStringContainsString('Loading', $output);
    }

    public function testStartStopFunctionality(): void
    {
        $element = LoadingElement::create($this->style, 'Test');

        $this->assertFalse($element->isRunning());

        $element->start();
        $this->assertTrue($element->isRunning());

        $element->stop();
        $this->assertFalse($element->isRunning());

        $output = $this->output->fetch();
        $this->assertStringContainsString('Test', $output);
    }

    public function testManualMode(): void
    {
        $element = LoadingElement::create($this->style, 'Test');

        // Manual mode - no duration
        $element->render(); // Returns void
        $this->assertTrue($element->isRunning());

        $element->stop();
        $this->assertFalse($element->isRunning());

        $output = $this->output->fetch();
        $this->assertStringContainsString('Test', $output);
    }

    public function testDotsToggle(): void
    {
        // Test with dots (default)
        $element = LoadingElement::create($this->style, 'Test')
            ->showDots()
            ->start();

        $output = $this->output->fetch();
        $this->assertStringContainsString('…', $output);

        $element->stop();

        // Reset output
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);

        // Test without dots
        $element = LoadingElement::create($this->style, 'Test')
            ->hideDots()
            ->start();

        $output = $this->output->fetch();
        $this->assertStringNotContainsString('…', $output);

        $element->stop();
    }

    public function testColorFunctionality(): void
    {
        $element = LoadingElement::create($this->style, 'Colored Test')
            ->setColor('green');

        // Test that color is set (we can't test actual color output with BufferedOutput)
        $this->assertInstanceOf(LoadingElement::class, $element);

        $element->start();
        $output = $this->output->fetch();
        $this->assertStringContainsString('Colored Test', $output);

        $element->stop();
    }

    public function testCharacterSets(): void
    {
        $element = LoadingElement::create($this->style, 'Test');

        // Test setting character set
        $element->setCharacterSet(LoadingCharacterSet::DOTS);
        $this->assertInstanceOf(LoadingElement::class, $element);
    }

    public function testStaticFactoryMethods(): void
    {
        $methods = [
            'stars' => LoadingCharacterSet::STARS,
            'braille' => LoadingCharacterSet::BRAILLE,
            'dots' => LoadingCharacterSet::DOTS,
            'arrows' => LoadingCharacterSet::ARROWS,
            'bars' => LoadingCharacterSet::BARS,
        ];

        foreach ($methods as $method => $constant) {
            $element = LoadingElement::{$method}($this->style, 'Test');
            $this->assertInstanceOf(LoadingElement::class, $element);

            $element->start();
            $output = $this->output->fetch();
            $this->assertStringContainsString('Test', $output);
            $element->stop();

            // Reset output for next test
            $this->output = new BufferedOutput();
            $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
        }
    }

    public function testEnumValues(): void
    {
        $this->assertEquals('stars', LoadingCharacterSet::STARS->value);
        $this->assertEquals('braille', LoadingCharacterSet::BRAILLE->value);
        $this->assertEquals('dots', LoadingCharacterSet::DOTS->value);
        $this->assertEquals('arrows', LoadingCharacterSet::ARROWS->value);
        $this->assertEquals('bars', LoadingCharacterSet::BARS->value);
    }

    public function testEnumGetChars(): void
    {
        $starsChars = LoadingCharacterSet::STARS->getChars();
        $this->assertIsArray($starsChars);
        $this->assertNotEmpty($starsChars);

        $brailleChars = LoadingCharacterSet::BRAILLE->getChars();
        $this->assertIsArray($brailleChars);
        $this->assertNotEmpty($brailleChars);
    }
}
