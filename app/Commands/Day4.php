<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class Day4 extends Command
{
    public const string XMAS = 'XMAS';
    public const array MAS_MATRIX = [
        [ 'M', null, 'S'],
        [null, 'A', null],
        ['M', null, 'S'],
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day4';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code - Day 4';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Day 4 - Part 1');
        $matrix = $this->processFile();

        $totalWords = 0;
        foreach ($matrix as $lineNumber => $line) {
            foreach ($line as $letterNumber => $letter) {
                $totalWords +=$this->checkAdjacent($matrix, $lineNumber, $letterNumber, self::XMAS);
            }
        }

        $this->info("Total words: $totalWords");

        $this->info('Day 4 - Part 2');

        $totalWords = 0;

        foreach ($matrix as $lineNumber => $line) {
            foreach ($line as $letterNumber => $letter) {
                $totalWords +=$this->checkMatrix($matrix, $lineNumber, $letterNumber, self::MAS_MATRIX);
            }
        }

        $this->info("Total words: $totalWords");
    }

    protected function processFile(): array
    {
        $file = file_get_contents(__DIR__ . '/../Data/day4.txt');

        //split into array of lines
        $lines = explode("\n", $file);

        //split each line into array of letters
        $lines = array_map(function($line) {
            return str_split($line);
        }, $lines);

        return $lines;
    }

    private function checkMatrix(array $matrix, $lineNumber, $letterNumber, array $checkMatrix): int
    {
        $rotations = [
            0 => $checkMatrix,
            90 => $this->rotateMatrix($checkMatrix),
            180 => $this->rotateMatrix($this->rotateMatrix($checkMatrix)),
            270 => $this->rotateMatrix($this->rotateMatrix($this->rotateMatrix($checkMatrix))),
        ];


        $totalWords = 0;

        $startingRow = 1;
        $startingColumn = 1;

        foreach ($rotations as $angle => $rotation) {
            $row = $lineNumber;
            $column = $letterNumber;

            if ($matrix[$row][$column] !== $rotation[$startingRow][$startingColumn]) {
                continue;
            }

            $found = true;

            for ($i = 0; $i < count($rotation); $i++) {
                for ($j = 0; $j < count($rotation[$i]); $j++) {
                    if ($rotation[$i][$j] === null) {
                        continue;
                    }

                    $offsetRow = $i - $startingRow;
                    $offsetColumn = $j - $startingColumn;

                    $rotationLetter = $rotation[$i][$j];
                    $matrixLetter = $matrix[$row + $offsetRow][$column + $offsetColumn] ?? null;

                    if ($rotationLetter !== $matrixLetter) {
                        $found = false;
                        break;
                    }
                }

                if (!$found) {
                    break;
                }
            }

            if ($found) {
                $totalWords++;
            }
        }

        return $totalWords;
    }

    private function checkAdjacent(array $matrix, int $lineNumber, int $letterNumber, string $word = ''): int
    {
        // Skip if not the first letter of the word
        if ($matrix[$lineNumber][$letterNumber] !== $word[0]) {
            return 0;
        }

        // check direction
        $directions = [
            'right' => [0, 1], // right
            'left' => [0, -1], // left
            'down' => [1, 0], // down
            'up' => [-1, 0], // up
            'down right' => [1, 1], // down right
            'down left' => [1, -1], // down left
            'up right' => [-1, 1], // up right
            'up left' => [-1, -1], // up left
        ];

        $foundWords = 0;

        foreach ($directions as $direction => $coords) {
            $row = $lineNumber;
            $column = $letterNumber;
            $wordIndex = 0;
            $found = true;

            while ($found && $wordIndex < strlen($word)-1) {
                $row += $coords[0];
                $column += $coords[1];
                $wordIndex++;

                $letter = $matrix[$row][$column] ?? null;

                // If the letter is not the same as the next letter in the word stop checking, stop checking
                if ($letter !== $word[$wordIndex]) {
                    $found = false;
                }
            }

            if ($found) {
                $foundWords ++;
            }
        }

        return $foundWords;
    }

    // Rotate the matrix 90 degrees
    private function rotateMatrix(array $checkMatrix): array
    {
        $rotatedMatrix = [];
        $size = count($checkMatrix);

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $rotatedMatrix[$i][$j] = $checkMatrix[$size - $j - 1][$i];
            }
        }

        return $rotatedMatrix;
    }
}
