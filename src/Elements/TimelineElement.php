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

use ConsoleStyleKit\Enums\TimelineOrientation;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * TimelineElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class TimelineElement extends AbstractStyleElement
{
    /**
     * @var array<int, array{date: string, event: string}>
     */
    private array $events = [];
    private string $connector = '●';
    private string $line = '-';
    private TimelineOrientation $orientation = TimelineOrientation::HORIZONTAL;
    private ?string $connectorColor = null;

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public function setEvents(array $events): self
    {
        $this->events = $events;

        return $this;
    }

    public function addEvent(string $date, string $event): self
    {
        $this->events[] = ['date' => $date, 'event' => $event];

        return $this;
    }

    public function setConnector(string $connector): self
    {
        $this->connector = $connector;

        return $this;
    }

    public function setLine(string $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function setOrientation(TimelineOrientation|string $orientation): self
    {
        $this->orientation = $orientation instanceof TimelineOrientation ? $orientation : TimelineOrientation::from($orientation);

        return $this;
    }

    public function setConnectorColor(?string $color): self
    {
        $this->connectorColor = $color;

        return $this;
    }

    public function render(): void
    {
        if ($this->verboseOnly && !$this->style->isVerbose()) {
            return;
        }

        if (empty($this->events)) {
            return;
        }

        if (TimelineOrientation::VERTICAL === $this->orientation) {
            $this->renderVertical();
        } else {
            $this->renderHorizontal();
        }
    }

    private function renderHorizontal(): void
    {
        $width = $this->getTerminalWidth();
        $eventCount = count($this->events);

        if (1 === $eventCount) {
            $this->renderSingleHorizontalEvent();

            return;
        }

        // Calculate available width and positions
        $availableWidth = max(50, $width - 10);
        $maxLabelLength = 12; // Truncate long labels

        // Truncate and prepare labels
        $dates = [];
        $eventTexts = [];
        $maxDateLen = 0;
        $maxEventLen = 0;

        foreach ($this->events as $event) {
            $date = substr($event['date'], 0, $maxLabelLength);
            $eventText = substr($event['event'], 0, $maxLabelLength);

            $dates[] = $date;
            $eventTexts[] = $eventText;
            $maxDateLen = max($maxDateLen, strlen($date));
            $maxEventLen = max($maxEventLen, strlen($eventText));
        }

        // Calculate spacing between points
        $totalContentWidth = max($maxDateLen, $maxEventLen) * $eventCount;
        $remainingWidth = $availableWidth - $totalContentWidth;
        $spacing = (int) max(5, $remainingWidth / ($eventCount - 1));

        // Build timeline strings
        $dateRow = '';
        $timelineRow = '';
        $eventRow = '';

        for ($i = 0; $i < $eventCount; ++$i) {
            $date = $dates[$i];
            $eventText = $eventTexts[$i];

            // Add content
            $dateRow .= $date;
            $timelineRow .= $this->formatConnector();
            $eventRow .= $eventText;

            // Add spacing and lines (except for last item)
            if ($i < $eventCount - 1) {
                // Pad to align with next item
                $currentDateLen = strlen($date);
                $currentEventLen = strlen($eventText);
                $nextDateLen = strlen($dates[$i + 1]);
                $nextEventLen = strlen($eventTexts[$i + 1]);

                $dateSpacing = $spacing + (int) (($nextDateLen - $currentDateLen) / 2);
                $eventSpacing = $spacing + (int) (($nextEventLen - $currentEventLen) / 2);

                $dateRow .= str_repeat(' ', max(1, $dateSpacing));
                $timelineRow .= str_repeat($this->line, max(1, $spacing));
                $eventRow .= str_repeat(' ', max(1, $eventSpacing));
            }
        }

        // Render the three rows
        $this->style->writeln($dateRow);
        $this->style->writeln($timelineRow);
        $this->style->writeln($eventRow);
    }

    private function renderSingleHorizontalEvent(): void
    {
        $date = $this->events[0]['date'];
        $event = $this->events[0]['event'];

        // Simple single event display
        $this->style->writeln($date);
        $this->style->writeln($this->formatConnector());
        $this->style->writeln($event);
    }

    private function formatConnector(): string
    {
        return $this->connectorColor
            ? "<fg={$this->connectorColor}>{$this->connector}</>"
            : $this->connector;
    }

    private function renderVertical(): void
    {
        foreach ($this->events as $index => $event) {
            $date = $event['date'];
            $eventText = $event['event'];

            // Format: ● 2024-01-01 ─ Project Start
            $connector = $this->formatConnector();
            $this->style->writeln("{$connector} {$date} ─ {$eventText}");

            // Add connecting line (except for last event)
            if ($index < count($this->events) - 1) {
                $connectorLine = $this->connectorColor ? "<fg={$this->connectorColor}>│</>" : '│';
                $this->style->writeln($connectorLine);
            }
        }
    }

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public static function create(SymfonyStyle $style, array $events = [], bool $verboseOnly = false): self
    {
        return (new self($style, $verboseOnly))->setEvents($events);
    }

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public static function withDots(SymfonyStyle $style, array $events = []): self
    {
        return self::create($style, $events)
            ->setConnector('•')
            ->setLine('.');
    }

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public static function withStars(SymfonyStyle $style, array $events = []): self
    {
        return self::create($style, $events)
            ->setConnector('★')
            ->setLine('─');
    }

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public static function vertical(SymfonyStyle $style, array $events = [], bool $verboseOnly = false): self
    {
        return self::create($style, $events, $verboseOnly)
            ->setOrientation(TimelineOrientation::VERTICAL);
    }

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public static function horizontal(SymfonyStyle $style, array $events = [], bool $verboseOnly = false): self
    {
        return self::create($style, $events, $verboseOnly)
            ->setOrientation(TimelineOrientation::HORIZONTAL);
    }

    public function __toString(): string
    {
        if (empty($this->events)) {
            return '';
        }

        ob_start();
        if (TimelineOrientation::VERTICAL === $this->orientation) {
            $this->renderVerticalToString();
        } else {
            $this->renderHorizontalToString();
        }
        $output = ob_get_clean();

        return $output ?: '';
    }

    private function renderHorizontalToString(): void
    {
        $eventCount = count($this->events);

        if (1 === $eventCount) {
            $date = $this->events[0]['date'];
            $event = $this->events[0]['event'];

            echo $date."\n";
            echo $this->connector."\n";
            echo $event;

            return;
        }

        // Use same logic as render method
        $availableWidth = 60;
        $maxLabelLength = 12;

        // Truncate and prepare labels
        $dates = [];
        $eventTexts = [];

        foreach ($this->events as $event) {
            $dates[] = substr($event['date'], 0, $maxLabelLength);
            $eventTexts[] = substr($event['event'], 0, $maxLabelLength);
        }

        // Calculate spacing
        $spacing = max(5, (int) ($availableWidth / $eventCount));

        // Build timeline strings
        $dateRow = '';
        $timelineRow = '';
        $eventRow = '';

        for ($i = 0; $i < $eventCount; ++$i) {
            $date = $dates[$i];
            $eventText = $eventTexts[$i];

            $dateRow .= $date;
            $timelineRow .= $this->connector;
            $eventRow .= $eventText;

            if ($i < $eventCount - 1) {
                $dateRow .= str_repeat(' ', max(1, $spacing - strlen($date)));
                $timelineRow .= str_repeat($this->line, max(1, $spacing - 1));
                $eventRow .= str_repeat(' ', max(1, $spacing - strlen($eventText)));
            }
        }

        echo $dateRow."\n";
        echo $timelineRow."\n";
        echo $eventRow;
    }

    private function renderVerticalToString(): void
    {
        $lines = [];
        foreach ($this->events as $index => $event) {
            $date = $event['date'];
            $eventText = $event['event'];

            // Format: ● 2024-01-01 ─ Project Start
            $lines[] = "{$this->connector} {$date} ─ {$eventText}";

            // Add connecting line (except for last event)
            if ($index < count($this->events) - 1) {
                $lines[] = '│';
            }
        }

        echo implode("\n", $lines);
    }
}
