<?php

declare(strict_types=1);

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

use ConsoleStyleKit\Enums\LoadingCharacterSet;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * LoadingElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class LoadingElement extends AbstractStyleElement
{
    private const ANIMATION_DELAY = 0.2;

    private string $text = 'Loading';
    private bool $showDots = true;
    private ?string $color = null;
    private LoadingCharacterSet $charSet = LoadingCharacterSet::STARS;
    private bool $isRunning = false;
    private int $charIndex = 0;
    private float $lastUpdate = 0;
    private bool $autoUpdate = false;
    private mixed $tickFunction = null;

    public static function create(SymfonyStyle $style, string $text = 'Loading', bool $verboseOnly = false): self
    {
        return (new self($style, $verboseOnly))->setText($text);
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function hideDots(): self
    {
        $this->showDots = false;

        return $this;
    }

    public function showDots(): self
    {
        $this->showDots = true;

        return $this;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setCharacterSet(LoadingCharacterSet $charSet): self
    {
        $this->charSet = $charSet;

        return $this;
    }

    public function enableAutoUpdate(): self
    {
        $this->autoUpdate = true;

        return $this;
    }

    public function disableAutoUpdate(): self
    {
        $this->autoUpdate = false;

        return $this;
    }

    public static function braille(SymfonyStyle $style, string $text = 'Loading'): self
    {
        return self::create($style, $text)->setCharacterSet(LoadingCharacterSet::BRAILLE);
    }

    public static function dots(SymfonyStyle $style, string $text = 'Loading'): self
    {
        return self::create($style, $text)->setCharacterSet(LoadingCharacterSet::DOTS);
    }

    public static function arrows(SymfonyStyle $style, string $text = 'Loading'): self
    {
        return self::create($style, $text)->setCharacterSet(LoadingCharacterSet::ARROWS);
    }

    public static function bars(SymfonyStyle $style, string $text = 'Loading'): self
    {
        return self::create($style, $text)->setCharacterSet(LoadingCharacterSet::BARS);
    }

    public static function stars(SymfonyStyle $style, string $text = 'Loading'): self
    {
        return self::create($style, $text)->setCharacterSet(LoadingCharacterSet::STARS);
    }

    public function start(): self
    {
        $this->isRunning = true;
        $this->charIndex = 0;
        $this->lastUpdate = microtime(true);
        $this->displayFrame($this->charSet->getChars()[0]); // Show first frame
        $this->startBackgroundAnimation();

        return $this;
    }

    public function update(): self
    {
        if (!$this->isRunning) {
            return $this;
        }

        $now = microtime(true);
        if ($now - $this->lastUpdate >= self::ANIMATION_DELAY) {
            $chars = $this->charSet->getChars();
            $this->charIndex = ($this->charIndex + 1) % count($chars);
            $this->lastUpdate = $now;
            $this->displayFrame($chars[$this->charIndex]);
        }

        return $this;
    }

    private function startBackgroundAnimation(): void
    {
        // Register shutdown function to clean up
        register_shutdown_function(function () {
            if ($this->isRunning) {
                $this->stop();
            }
        });

        // If auto-update is enabled, start a non-blocking animation loop
        if ($this->autoUpdate) {
            $this->runAutoUpdateLoop();
        }
    }

    private function runAutoUpdateLoop(): void
    {
        if (!$this->autoUpdate) {
            return;
        }

        // Simple and effective: Register a tick function without throttling
        // Let the internal update() method handle all timing logic
        $loadingInstance = $this;

        $this->tickFunction = function () use ($loadingInstance) {
            if ($loadingInstance->isRunning()) {
                $loadingInstance->update();
            }
        };

        // Register the tick function - this works with declare(ticks=1)
        register_tick_function($this->tickFunction);
    }

    private function stopBackgroundAnimation(): void
    {
        if (null !== $this->tickFunction) {
            unregister_tick_function($this->tickFunction);
            $this->tickFunction = null;
        }
    }

    public function stop(): self
    {
        if ($this->isRunning) {
            $this->isRunning = false;
            $this->stopBackgroundAnimation();
            // Clear the loading line and move to next line
            $this->style->write("\r".str_repeat(' ', 50)."\r");
            $this->style->newLine();
        }

        return $this;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }

    public function __toString(): string
    {
        // For LoadingElement, return a single frame representation
        // since full animation doesn't make sense in string form
        $chars = $this->charSet->getChars();
        $char = $chars[0]; // Use first character
        $dots = $this->showDots ? '…' : '';
        $output = "{$char} {$this->text} {$dots}";

        if ($this->color) {
            // For string output, we include the color tags but they won't be rendered
            $output = "<fg={$this->color}>{$output}</fg={$this->color}>";
        }

        return $output;
    }

    public function render(?int $duration = null): void
    {
        if (null !== $duration) {
            // Fixed duration mode
            $this->runAnimation($duration);
        } else {
            // Manual mode - just start, user calls update() and stop()
            $this->start();
        }
    }

    private function runAnimation(int $duration): void
    {
        $chars = $this->charSet->getChars();
        $startTime = microtime(true);
        $charIndex = 0;
        $lastUpdate = 0;

        while ((microtime(true) - $startTime) < $duration) {
            $now = microtime(true);
            if ($now - $lastUpdate >= self::ANIMATION_DELAY) {
                $this->displayFrame($chars[$charIndex]);
                $charIndex = ($charIndex + 1) % count($chars);
                $lastUpdate = $now;
            }
            usleep(10000); // Small sleep to prevent CPU overuse
        }

        // Clear and move to next line
        $this->style->write("\r".str_repeat(' ', 50)."\r");
        $this->style->newLine();
    }

    private function displayFrame(string $char): void
    {
        $dots = $this->showDots ? '…' : '';
        $output = "{$char} {$this->text} {$dots}";

        if ($this->color) {
            $output = "<fg={$this->color}>{$output}</fg={$this->color}>";
        }

        $this->style->write("\r".str_pad($output, 50)."\r{$output}");
    }
}
