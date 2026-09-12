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

use ConsoleStyleKit\Elements\TimelineElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * TimelineElementTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class TimelineElementTest extends TestCase
{
    private SymfonyStyle $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new SymfonyStyle(new ArrayInput([]), $this->output);
    }

    public function testEmptyTimeline(): void
    {
        $element = new TimelineElement($this->style);
        $element->render();

        $output = $this->output->fetch();
        $this->assertEmpty(trim($output));
    }

    public function testSingleEvent(): void
    {
        $element = new TimelineElement($this->style);
        $element->setEvents([['date' => '2024-01-01', 'event' => 'Start']])->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('2024-01-01', $output);
        $this->assertStringContainsString('Start', $output);
        $this->assertStringContainsString('●', $output);
    }

    public function testMultipleEvents(): void
    {
        $events = [
            ['date' => '2024-01-01', 'event' => 'Start'],
            ['date' => '2024-06-01', 'event' => 'Middle'],
            ['date' => '2024-12-01', 'event' => 'End'],
        ];

        $element = new TimelineElement($this->style);
        $element->setEvents($events)->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('2024-01-01', $output);
        $this->assertStringContainsString('Start', $output);
        $this->assertStringContainsString('Middle', $output);
        $this->assertStringContainsString('End', $output);
    }

    public function testAddEvent(): void
    {
        $element = new TimelineElement($this->style);
        $result = $element->addEvent('2024-01-01', 'Test Event');

        $this->assertSame($element, $result);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('Test Event', $output);
    }

    public function testCustomConnector(): void
    {
        $element = new TimelineElement($this->style);
        $element->addEvent('2024-01-01', 'Test')
                ->setConnector('★')
                ->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('★', $output);
    }

    public function testCustomLine(): void
    {
        $element = new TimelineElement($this->style);
        $element->addEvent('2024-01-01', 'Test')
                ->addEvent('2024-02-01', 'Test2')
                ->setLine('=')
                ->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('=', $output);
    }

    public function testFluentInterface(): void
    {
        $element = new TimelineElement($this->style);

        $result = $element->setEvents([]);
        $this->assertSame($element, $result);

        $result = $element->setConnector('*');
        $this->assertSame($element, $result);

        $result = $element->setLine('~');
        $this->assertSame($element, $result);
    }

    public function testStaticCreate(): void
    {
        $events = [['date' => '2024-01-01', 'event' => 'Test']];
        $element = TimelineElement::create($this->style, $events);

        $this->assertInstanceOf(TimelineElement::class, $element);

        $element->render();
        $output = $this->output->fetch();
        $this->assertStringContainsString('Test', $output);
    }

    public function testStaticWithDots(): void
    {
        $events = [['date' => '2024-01-01', 'event' => 'Test']];
        $element = TimelineElement::withDots($this->style, $events);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('•', $output);
    }

    public function testStaticWithStars(): void
    {
        $events = [['date' => '2024-01-01', 'event' => 'Test']];
        $element = TimelineElement::withStars($this->style, $events);
        $element->render();

        $output = $this->output->fetch();
        $this->assertStringContainsString('★', $output);
    }
}
