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

use ConsoleStyleKit\ConsoleStyleKit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * ConsoleStyleKitTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class ConsoleStyleKitTest extends TestCase
{
    private ConsoleStyleKit $style;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->style = new ConsoleStyleKit(new ArrayInput([]), $this->output);
    }

    public function testLegacyBlockquote(): void
    {
        $this->style->blockquote('Test message');
        $output = $this->output->fetch();

        $this->assertStringContainsString('Test message', $output);
        $this->assertStringContainsString('│', $output);
        $this->assertStringContainsString('╷', $output); // top border
        $this->assertStringContainsString('╵', $output); // bottom border
    }

    public function testLegacyRating(): void
    {
        $this->style->rating(5, 3);
        $output = $this->output->fetch();

        $this->assertStringContainsString('●', $output);
    }

    public function testLegacyBadge(): void
    {
        $this->style->badge('TEST', 'blue');
        $output = $this->output->fetch();

        $this->assertStringContainsString('TEST', $output);
    }

    public function testLegacySeparator(): void
    {
        $this->style->separator();
        $output = $this->output->fetch();

        $this->assertStringContainsString('-', $output);
    }

    public function testLegacyKeyValue(): void
    {
        $this->style->keyValue('Key', 'Value');
        $output = $this->output->fetch();

        $this->assertStringContainsString('Key', $output);
        $this->assertStringContainsString('Value', $output);
    }

    public function testLegacyTimeline(): void
    {
        $events = [['date' => '2024-01-01', 'event' => 'Test']];
        $this->style->timeline($events);
        $output = $this->output->fetch();

        $this->assertStringContainsString('2024-01-01', $output);
        $this->assertStringContainsString('Test', $output);
    }

    public function testBackwardCompatibility(): void
    {
        // Test that old API still works exactly the same
        $this->style->blockquote('Legacy test', 'WARNING');
        $this->style->rating(5, 4, 'bar', true);
        $this->style->badge('OLD', 'red');

        $output = $this->output->fetch();

        $this->assertStringContainsString('Legacy test', $output);
        $this->assertStringContainsString('WARNING', $output);
        $this->assertStringContainsString('#', $output);
        $this->assertStringContainsString('OLD', $output);
    }
}
