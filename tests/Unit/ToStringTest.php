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
use ConsoleStyleKit\Elements\BlockquoteElement;
use ConsoleStyleKit\Elements\LoadingElement;
use ConsoleStyleKit\Elements\RatingElement;
use ConsoleStyleKit\Enums\BadgeColor;
use ConsoleStyleKit\Enums\BlockquoteType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ToStringTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class ToStringTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testBlockquoteElementToString(): void
    {
        $element = BlockquoteElement::create($this->style, 'Test message', BlockquoteType::WARNING);
        $string = (string) $element;

        $this->assertStringContainsString('WARNING', $string);
        $this->assertStringContainsString('Test message', $string);
        $this->assertStringContainsString('│', $string);
        $this->assertStringContainsString('╷', $string); // top border
        $this->assertStringContainsString('╵', $string); // bottom border
    }

    public function testBadgeElementToString(): void
    {
        $element = BadgeElement::create($this->style, 'SUCCESS', BadgeColor::GREEN);
        $string = (string) $element;

        $this->assertStringContainsString('SUCCESS', $string);
    }

    public function testRatingElementToString(): void
    {
        $element = RatingElement::circle($this->style, 5, 3);
        $string = (string) $element;

        $this->assertStringContainsString('●', $string); // Filled circles
        $this->assertStringContainsString('○', $string); // Empty circles
    }

    public function testLoadingElementToString(): void
    {
        $element = LoadingElement::create($this->style, 'Loading Test');
        $string = (string) $element;

        $this->assertStringContainsString('Loading Test', $string);
        $this->assertStringContainsString('·', $string); // First STARS character
        $this->assertStringContainsString('…', $string); // Dots
    }

    public function testLoadingElementToStringWithoutDots(): void
    {
        $element = LoadingElement::create($this->style, 'No Dots')->hideDots();
        $string = (string) $element;

        $this->assertStringContainsString('No Dots', $string);
        $this->assertStringNotContainsString('…', $string);
    }

    public function testLoadingElementToStringWithColor(): void
    {
        $element = LoadingElement::create($this->style, 'Colored')->setColor('red');
        $string = (string) $element;

        $this->assertStringContainsString('<fg=red>', $string);
        $this->assertStringContainsString('Colored', $string);
    }

    public function testMagicToString(): void
    {
        $element = BadgeElement::create($this->style, 'MAGIC', BadgeColor::BLUE);

        // Test magic __toString() method
        $magicString = (string) $element;
        $directString = $element->__toString();

        $this->assertSame($directString, $magicString);
        $this->assertStringContainsString('MAGIC', $magicString);
    }

    public function testStringInterpolation(): void
    {
        $badge = BadgeElement::create($this->style, 'TEST', BadgeColor::RED);
        $interpolated = "Badge: {$badge}";

        $this->assertStringContainsString('Badge:', $interpolated);
        $this->assertStringContainsString('TEST', $interpolated);
    }

    public function testStringConcatenation(): void
    {
        $badge1 = BadgeElement::create($this->style, 'FIRST', BadgeColor::GREEN);
        $badge2 = BadgeElement::create($this->style, 'SECOND', BadgeColor::BLUE);

        $combined = $badge1.' '.$badge2;

        $this->assertStringContainsString('FIRST', $combined);
        $this->assertStringContainsString('SECOND', $combined);
    }
}
