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

use ConsoleStyleKit\Enums\BlockquoteType;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * BlockquoteElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-3.0-or-later
 */
class BlockquoteElement extends AbstractStyleElement
{
    private string $text;
    private ?BlockquoteType $type = null;

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setType(BlockquoteType|string|null $type): self
    {
        $this->type = $type instanceof BlockquoteType ? $type : ($type ? BlockquoteType::from($type) : null);

        return $this;
    }

    public function __toString(): string
    {
        $wrappedText = $this->wrapText($this->text);

        if (null === $this->type) {
            return $this->formatLinesWithBorders($wrappedText, 'gray');
        }

        $color = $this->type->getColor();
        $bold = $this->type->isBold() ? ';options=bold' : '';

        $result = "<fg={$color}>╷</>\n";
        $result .= "<fg={$color}{$bold}>│</> <fg={$color}{$bold}>{$this->type->value}</>\n";
        $result .= $this->formatLines($wrappedText, $color)."\n";
        $result .= "<fg={$color}>╵</>";

        return $result;
    }

    /**
     * @return array<string>
     */
    private function wrapText(string $text, int $maxWidth = 70): array
    {
        // First, split by explicit line breaks
        $paragraphs = explode("\n", $text);
        $lines = [];

        foreach ($paragraphs as $paragraph) {
            // Handle empty lines (double \n creates empty paragraphs)
            if ('' === trim($paragraph)) {
                $lines[] = '';
                continue;
            }

            // Word wrap each paragraph
            $words = explode(' ', trim($paragraph));
            $currentLine = '';

            foreach ($words as $word) {
                if (empty($currentLine)) {
                    $currentLine = $word;
                } elseif (strlen($currentLine.' '.$word) <= $maxWidth) {
                    $currentLine .= ' '.$word;
                } else {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                }
            }

            if (!empty($currentLine)) {
                $lines[] = $currentLine;
            }
        }

        return empty($lines) ? [''] : $lines;
    }

    /**
     * @param array<string> $lines
     */
    private function formatLines(array $lines, string $color): string
    {
        $result = '';
        foreach ($lines as $line) {
            $result .= "<fg={$color}>│</> {$line}\n";
        }

        return rtrim($result, "\n");
    }

    /**
     * @param array<string> $lines
     */
    private function formatLinesWithBorders(array $lines, string $color): string
    {
        if (empty($lines)) {
            return "<fg={$color}>╷</>\n<fg={$color}>╵</>";
        }

        $result = "<fg={$color}>╷</>\n";
        foreach ($lines as $line) {
            $result .= "<fg={$color}>│</> {$line}\n";
        }
        $result .= "<fg={$color}>╵</>";

        return $result;
    }

    public function render(): void
    {
        if ($this->verboseOnly && !$this->style->isVerbose()) {
            return;
        }
        $this->style->newLine();
        $this->style->writeln($this->__toString());
    }

    public static function create(SymfonyStyle $style, string $text, BlockquoteType|string|null $type = null, bool $verboseOnly = false): self
    {
        return (new self($style, $verboseOnly))
            ->setText($text)
            ->setType($type);
    }
}
