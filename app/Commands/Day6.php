<?php

namespace App\Commands;

use App\Objects\Guard;
use Exception;
use LaravelZero\Framework\Commands\Command;

class Day6 extends Command
{

    public const string OBSTACLE = '#';
    public const string MY_OBSTACLE = 'O';
    public const string OPEN = '.';
    public const string CLOSED = 'X';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day6';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code - Day 6';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Day 6 - Part 1');

        $matrix = $this->processFile();

        $guard = $this->processGaurdRoute($matrix);
        $totalSteps = collect($guard->history)->unique(fn($history) => [$history[0], $history[1]])->count();
        $this->info("Total steps: $totalSteps");

        $this->info('Day 6 - Part 2');

        $totalAttempts = 0;
        $totalFound = 0;

        foreach ( collect($guard->history)->unique(fn($history) => [$history[0], $history[1]]) as $history) {
            [$x, $y] = $history;

            if ($matrix[$x][$y] !== self::OPEN) {
                continue;
            }

            $totalAttempts++;
            if ($totalAttempts % 500 === 0) {
                $this->info("Total attempts: $totalAttempts");
            }

            $clone = collect($matrix)->toArray();
            $clone[$x][$y] = self::MY_OBSTACLE;

            try {
                $this->processGaurdRoute($clone, true);
            } catch (Exception) {
                $totalFound++;
            }

        }

        $this->info("Total attempts: $totalFound");

    }

    protected function processFile(): array
    {
        $file = file_get_contents(__DIR__ . '/../Data/day6.txt');

        //split into array of lines
        $lines = explode("\n", $file);

        //split each line into array of letters
        return array_map(fn($line) => str_split($line), $lines);
    }

    protected function processGaurdRoute(array $matrix, bool $throwOnOutOffBounds = false): Guard
    {
        $guard = $this->findGuard($matrix);

        do {
            [$x, $y] = $guard->nextCoordinate();
            if (in_array($matrix[$x][$y], [self::OBSTACLE, self::MY_OBSTACLE])) {
                $guard->turnLeft();
            } else {
                $guard->step();
            }
        } while ($this->nextStepAllowed($matrix, $guard, $throwOnOutOffBounds));

        return $guard;
    }

    private function findGuard(array $matrix): Guard
    {
        foreach ($matrix as $x => $line) {
            foreach ($line as $y => $cell) {
                if ($cell === Guard::UP || $cell === Guard::DOWN || $cell === Guard::LEFT || $cell === Guard::RIGHT) {
                    return new Guard($x, $y, $cell);
                }
            }
        }

        throw new Exception('Guard not found');
    }

    private function nextStepAllowed(array $matrix, Guard $guard, bool $throwOnHistory): bool
    {
        [$x, $y] = $guard->nextCoordinate();

        if ($guard->inHistory($x, $y, $guard->direction)) {
            if ($throwOnHistory) {
                throw new Exception('Already visited');
            }
            return false;
        }

        try {
            return isset($matrix[$x][$y]);
        } catch (Exception $e) {
            return false;
        }
    }

    protected function printMap(array $matrix): void
    {
        foreach ($matrix as $line) {
            $this->info(implode('', $line));
        }
    }
}
