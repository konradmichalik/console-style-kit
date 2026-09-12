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

declare(ticks=1);
require_once __DIR__.'/../vendor/autoload.php';

use ConsoleStyleKit\ConsoleStyleKit;
use ConsoleStyleKit\Elements\LoadingElement;
use ConsoleStyleKit\Enums\LoadingCharacterSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

// Create console style instance
$input = new ArgvInput();
$output = new ConsoleOutput();
$style = new ConsoleStyleKit($input, $output);

echo "Console Style Kit - Loading Animation Demo\n";
echo "==========================================\n\n";

echo "🎬 Loading Animation - Alle drei Modi\n";
echo "=====================================\n\n";

// Demo 1: Fixed Duration Mode
echo "1️⃣ Fixed Duration Mode (3 Sekunden):\n";
$style->loading('Automatische Animation', 3, 'green');
echo "✅ Abgeschlossen!\n\n";

// Demo 2: Manual Control Mode
echo "2️⃣ Manual Control Mode:\n";
$loading = $style->loading('Manuelle Kontrolle', null, 'blue')->start(); // start() hinzugefügt
for ($i = 0; $i < 50; ++$i) {
    // Simuliere Arbeit
    usleep(50000); // 0.05s
    $loading->update(); // Animation manuell aktualisieren
}
$loading->stop();
echo "✅ Abgeschlossen!\n\n";

// Demo 3: Auto-Update Mode
echo "3️⃣ Auto-Update Mode (während Code-Ausführung):\n";
echo "Animation läuft automatisch während Code-Ausführung - keine manuellen update() Aufrufe nötig!\n";
$loading = $style->loading('Automatische Updates', null, 'yellow')
    ->enableAutoUpdate()
    ->start(); // Wichtig: start() nach enableAutoUpdate() aufrufen!

for ($i = 0; $i < 250; ++$i) {
    // Schwere Berechnungen (jede Zeile triggert tick function)
    $dummy = sqrt($i * 1000);
    $dummy2 = log($i + 1);
    $dummy3 = sin($i);
    $dummy4 = cos($i);

    // Mehr Code-Zeilen = mehr Animation-Updates
    $array = range(1, 10);
    $sum = array_sum($array);
    $product = array_product(range(1, 3));
    $filtered = array_filter($array, fn ($x) => $x > 5);
    $mapped = array_map(fn ($x) => $x * 2, range(1, 5));

    // KEINE echo statements hier - die würden die Animation stören!

    // Realistische Pause für schwere Berechnungen
    usleep(20000); // 0.02s - total ~5s
}

$loading->stop();
echo "✅ Alle Modi demonstriert!\n\n";

echo "🌟 Empfehlungen:\n";
echo "• Fixed Duration: Für bekannte Laufzeiten\n";
echo "• Manual Control: Für beste Performance und Kontrolle\n";
echo "• Auto-Update: Für einfachste Integration (mit Performance-Overhead)\n";

exit;

// Demo 1: Legacy render method (fixed 5 seconds)
$style->title('Demo 1: Legacy render() method');
$style->text('Diese Animation läuft für 5 Sekunden...');
$style->loading('Lade legacy Daten');
$style->success('Legacy loading abgeschlossen!');
$style->newLine();

// Demo 2: Start/Stop API mit kurzer Arbeit
$style->title('Demo 2: Start/Stop API - Kurze Arbeit');
$style->text('Simuliere kurze Arbeit (2 Sekunden)...');

$loading = $style->createLoading()->setText('Verarbeite Daten');
$loading->start();

for ($i = 0; $i < 40; ++$i) { // 2 seconds at 0.05s per iteration
    $loading->update();
    usleep(50000); // 0.05 seconds
}

$loading->stop();
$style->success('Kurze Arbeit abgeschlossen!');
$style->newLine();

// Demo 3: Start/Stop API mit längerer Arbeit
$style->title('Demo 3: Start/Stop API - Längere Arbeit');
$style->text('Simuliere längere Arbeit (4 Sekunden)...');

$loading = $style->startLoading('Berechne komplexe Aufgabe');

for ($i = 0; $i < 80; ++$i) { // 4 seconds at 0.05s per iteration
    $loading->update();
    usleep(50000); // 0.05 seconds
}

$loading->stop();
$style->success('Längere Arbeit abgeschlossen!');
$style->newLine();

// Demo 4: Ohne Punkte
$style->title('Demo 4: Animation ohne Punkte');
$style->text('Animation ohne die drei Punkte (3 Sekunden)...');

$loading = $style->createLoading()
    ->setText('Verbinde zum Server')
    ->hideDots()
    ->start();

for ($i = 0; $i < 60; ++$i) { // 3 seconds
    $loading->update();
    usleep(50000);
}

$loading->stop();
$style->success('Verbindung hergestellt!');
$style->newLine();

// Demo 5: Fluent API
$style->title('Demo 5: Fluent API');
$style->text('Fluent API mit showStartLoading (2.5 Sekunden)...');

$loading = $style->showStartLoading('Synchronisiere Dateien');

for ($i = 0; $i < 50; ++$i) { // 2.5 seconds
    $loading->update();
    usleep(50000);
}

$loading->stop();
$style->success('Dateien synchronisiert!');
$style->newLine();

// Demo 6: Verschiedene Texte während der Animation
$style->title('Demo 6: Dynamischer Text während Animation');
$style->text('Text ändert sich während der Animation...');

$loading = $style->createLoading()->start();

$tasks = [
    'Initialisiere System',
    'Lade Konfiguration',
    'Verbinde zur Datenbank',
    'Synchronisiere Daten',
    'Finalisiere Setup',
];

foreach ($tasks as $task) {
    $loading->setText($task);

    for ($i = 0; $i < 20; ++$i) { // 1 second per task
        $loading->update();
        usleep(50000);
    }
}

$loading->stop();
$style->success('Alle Aufgaben abgeschlossen!');
$style->newLine();

// Demo 7: Farbige Animationen
$style->title('Demo 7: Farbige Animationen');
$style->text('Verschiedene Farben für die Animation...');

$colors = ['green', 'yellow', 'blue', 'magenta', 'cyan'];
$colorNames = ['Grün', 'Gelb', 'Blau', 'Magenta', 'Cyan'];

foreach ($colors as $index => $color) {
    $loading = $style->createLoading()
        ->setText("Lade in {$colorNames[$index]}")
        ->setColor($color)
        ->start();

    for ($i = 0; $i < 15; ++$i) { // 0.75 seconds per color
        $loading->update();
        usleep(50000);
    }

    $loading->stop();
}

$style->success('Alle farbigen Animationen abgeschlossen!');
$style->newLine();

// Demo 8: Mit Farbe über Convenience-Methode
$style->title('Demo 8: Convenience-Methode mit Farbe');
$style->text('Direkt mit startLoading und Farbe (2 Sekunden)...');

$loading = $style->startLoading('Verarbeite rot', 'red');

for ($i = 0; $i < 40; ++$i) { // 2 seconds
    $loading->update();
    usleep(50000);
}

$loading->stop();
$style->success('Rote Animation abgeschlossen!');
$style->newLine();

// Demo 9: Verschiedene Zeichensätze
$style->title('Demo 9: Verschiedene Zeichensätze');
$style->text('Verschiedene Animations-Stile...');

$charSets = [
    ['name' => 'Braille', 'const' => LoadingCharacterSet::BRAILLE, 'factory' => 'braille'],
    ['name' => 'Punkte', 'const' => LoadingCharacterSet::DOTS, 'factory' => 'dots'],
    ['name' => 'Pfeile', 'const' => LoadingCharacterSet::ARROWS, 'factory' => 'arrows'],
    ['name' => 'Balken', 'const' => LoadingCharacterSet::BARS, 'factory' => 'bars'],
    ['name' => 'Klassisch', 'const' => LoadingCharacterSet::CLASSIC, 'factory' => 'classic'],
];

foreach ($charSets as $set) {
    $style->text("-> {$set['name']}-Animation:");

    // Mit OOP API
    $loading = $style->createLoading()
        ->setText("Lade mit {$set['name']}")
        ->setCharacterSet($set['const'])
        ->start();

    for ($i = 0; $i < 15; ++$i) { // 0.75 seconds per style
        $loading->update();
        usleep(50000);
    }

    $loading->stop();
}

$style->success('Alle Zeichensätze demonstriert!');
$style->newLine();

// Demo 10: Factory-Methoden
$style->title('Demo 10: Factory-Methoden für Zeichensätze');
$style->text('Verwendung der Static-Factory-Methoden...');

$factoryMethods = [
    ['name' => 'Arrows Factory', 'method' => 'arrows'],
    ['name' => 'Classic Factory', 'method' => 'classic'],
    ['name' => 'Dots Factory', 'method' => 'dots'],
];

foreach ($factoryMethods as $method) {
    $loading = LoadingElement::{$method['method']}($style, $method['name'])
        ->setColor('cyan')
        ->start();

    for ($i = 0; $i < 10; ++$i) { // 0.5 seconds per method
        $loading->update();
        usleep(50000);
    }

    $loading->stop();
}

$style->success('Factory-Methoden demonstriert!');
$style->newLine();

// Demo 11: Mit Convenience-Methoden und Zeichensätzen
$style->title('Demo 11: Convenience-Methoden mit Zeichensätzen');
$style->text('startLoading mit verschiedenen Zeichensätzen...');

$loading = $style->startLoading('Classic Bars', 'green', LoadingCharacterSet::BARS);

for ($i = 0; $i < 20; ++$i) { // 1 second
    $loading->update();
    usleep(50000);
}

$loading->stop();
$style->success('Convenience mit Zeichensatz abgeschlossen!');
$style->newLine();

$style->info('Alle Loading-Animationen wurden demonstriert!');
$style->section('Verfügbare Zeichensätze:');
$style->listing([
    'BRAILLE: ⠋⠙⠹⠸⠼⠴⠦⠧⠇⠏ (Default, gleichmäßige Breite)',
    'DOTS: ●○●○●○●○ (Pulsierende Punkte)',
    'ARROWS: ←↖↑↗→↘↓↙ (Rotierende Pfeile)',
    'BARS: |/-\\ (Rotierende Balken)',
    'CLASSIC: *+x+ (Klassische ASCII-Zeichen)',
]);
$style->text('Intervall zwischen den Zeichen: 0.2 Sekunden');
$style->text('Features: Farbunterstützung, verschiedene Zeichensätze, start/stop-Kontrolle!');
