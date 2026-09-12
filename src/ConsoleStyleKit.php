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

namespace ConsoleStyleKit;

use ConsoleStyleKit\Elements\BadgeElement;
use ConsoleStyleKit\Elements\BlockquoteElement;
use ConsoleStyleKit\Elements\KeyValueElement;
use ConsoleStyleKit\Elements\LoadingElement;
use ConsoleStyleKit\Elements\RatingElement;
use ConsoleStyleKit\Elements\SeparatorElement;
use ConsoleStyleKit\Elements\TimelineElement;
use ConsoleStyleKit\Enums\BadgeColor;
use ConsoleStyleKit\Enums\BlockquoteType;
use ConsoleStyleKit\Enums\LoadingCharacterSet;
use ConsoleStyleKit\Enums\RatingStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ConsoleStyleKit.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class ConsoleStyleKit extends SymfonyStyle
{
    protected bool $isVerbose = false;

    public function __construct(
        InputInterface $input,
        OutputInterface $output,
    ) {
        $this->isVerbose = $output->isVerbose();
        parent::__construct($input, $output);
    }

    public function blockquote(string $text, ?string $type = null, bool $verboseOnly = false): void
    {
        $enumType = $type ? BlockquoteType::from($type) : null;
        BlockquoteElement::create($this, $text, $enumType, $verboseOnly)->render();
    }

    public function rating(int $max, int $current, string $style = 'circle', bool $colorful = false, bool $verboseOnly = false): void
    {
        $enumStyle = RatingStyle::from($style);
        if (RatingStyle::CIRCLE === $enumStyle) {
            RatingElement::circle($this, $max, $current, $colorful, $verboseOnly)->render();
        } else {
            RatingElement::bar($this, $max, $current, $colorful, $verboseOnly)->render();
        }
    }

    public function badge(string $text, string $color = 'gray', bool $verboseOnly = false): void
    {
        $enumColor = BadgeColor::from($color);
        BadgeElement::create($this, $text, $enumColor, $verboseOnly)->render();
    }

    public function separator(bool $verboseOnly = false): void
    {
        SeparatorElement::create($this, $verboseOnly)->render();
    }

    public function keyValue(string $key, string $value, ?string $keyColor = null, bool $verboseOnly = false): void
    {
        KeyValueElement::create($this, $key, $value, $keyColor, $verboseOnly)->render();
    }

    /**
     * @param array<int, array{date: string, event: string}> $events
     */
    public function timeline(array $events, bool $verboseOnly = false): void
    {
        TimelineElement::create($this, $events, $verboseOnly)->render();
    }

    public function loading(string $text = 'Loading', ?int $duration = null, ?string $color = null, LoadingCharacterSet $charSet = LoadingCharacterSet::STARS, bool $verboseOnly = false): LoadingElement
    {
        $element = LoadingElement::create($this, $text, $verboseOnly)
            ->setColor($color)
            ->setCharacterSet($charSet);

        if (null !== $duration) {
            // Fixed duration mode - render and return element for potential chaining
            $element->render($duration);
        }
        // Manual mode - DON'T start automatically, let user call enableAutoUpdate() first
        // The user can call ->start() or ->enableAutoUpdate() after this

        return $element;
    }
}
