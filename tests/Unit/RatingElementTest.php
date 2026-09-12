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

use ConsoleStyleKit\Elements\RatingElement;
use ConsoleStyleKit\Enums\RatingStyle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * RatingElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class RatingElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testCircleRating(): void
    {
        $element = new RatingElement($this->style);
        $element->setRating(5, 3)->setStyle(RatingStyle::CIRCLE)->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('●', $output);
        $this->assertStringContainsString('○', $output);
    }

    public function testBarRating(): void
    {
        $element = new RatingElement($this->style);
        $element->setRating(5, 3)->setStyle(RatingStyle::BAR)->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('#', $output);
        $this->assertStringContainsString('-', $output);
        $this->assertStringContainsString('[', $output);
        $this->assertStringContainsString(']', $output);
    }

    public function testFluentInterface(): void
    {
        $element = new RatingElement($this->style);

        $result = $element->setRating(5, 3);
        $this->assertSame($element, $result);

        $result = $element->setStyle(RatingStyle::CIRCLE);
        $this->assertSame($element, $result);

        $result = $element->setColorful(true);
        $this->assertSame($element, $result);
    }

    public function testStaticCreate(): void
    {
        $element = RatingElement::create($this->style, 5, 3);
        $this->assertInstanceOf(RatingElement::class, $element);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('●', $output);
    }

    public function testStaticCircle(): void
    {
        $element = RatingElement::circle($this->style, 5, 4);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('●', $output);
        $this->assertStringContainsString('○', $output);
    }

    public function testStaticBar(): void
    {
        $element = RatingElement::bar($this->style, 5, 2);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('#', $output);
        $this->assertStringContainsString('-', $output);
    }

    public function testColorfulRating(): void
    {
        $element = RatingElement::circle($this->style, 5, 5, true);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('●', $output);
    }

    public function testConstants(): void
    {
        $this->assertEquals('circle', RatingStyle::CIRCLE->value);
        $this->assertEquals('bar', RatingStyle::BAR->value);
    }

    public function testRatingBounds(): void
    {
        // Test empty rating
        $element = RatingElement::create($this->style, 5, 0);
        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('○', $output);
        $this->assertStringNotContainsString('●', $output);

        // Reset output
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);

        // Test full rating
        $element = RatingElement::create($this->style, 5, 5);
        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('●', $output);
        $this->assertStringNotContainsString('○', $output);
    }
}
