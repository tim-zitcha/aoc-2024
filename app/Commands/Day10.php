<?php

namespace App\Commands;

use App\Enums\Direction;
use App\Traits\ProcessFile;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class Day10 extends Command
{
    use ProcessFile;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code - Day 10';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Day 10 - Part 1');

        $data = $this->processFile('day10', fn($line) => (new Collection(str_split($line)))->map(fn($value) => $value));

        $startingPoints = $data->reduce(function (Collection $carry, Collection $row, $rowIndex) {
            $row->filter(fn($value): bool => $value === '0')
                ->each(function ($value, $columnIndex) use ($rowIndex, $carry): void {
                    $carry->push([$rowIndex, $columnIndex]);
                });
            return $carry;
        }, new Collection());

        $totalTrailheads = $startingPoints->reduce(function (int $carry, $point) use ($data) {
            $this->info('Starting Point: ' . $point[0] . ', ' . $point[1]);

            $trailHeads = [];
            foreach (Direction::cases() as $newDirection) {
                $this->findNext($data, $point, $newDirection, 1, 9, $trailHeads);
            }

            return $carry + collect($trailHeads)->unique()->count();
        }, 0);

        $this->info('Total Paths: ' . $totalTrailheads);

        $this->info('Day 10 - Part 2');

        $totalTrailheads = $startingPoints->reduce(function (int $carry, $point) use ($data) {
            $this->info('Starting Point: ' . $point[0] . ', ' . $point[1]);

            $trailHeads = [];
            foreach (Direction::cases() as $newDirection) {
                $this->findNext($data, $point, $newDirection, 1, 9, $trailHeads);
            }

            return $carry + collect($trailHeads)->count();
        }, 0);

        $this->info('Total Paths: ' . $totalTrailheads);

    }

    protected function findNext($data, $startingPoint, Direction $direction, int $nextValue, int $maxValue, array &$trailEndPoints): bool
    {
        $nextPoint = match ($direction) {
            Direction::Up => [$startingPoint[0] - 1, $startingPoint[1]],
            Direction::Down => [$startingPoint[0] + 1, $startingPoint[1]],
            Direction::Left => [$startingPoint[0], $startingPoint[1] - 1],
            Direction::Right => [$startingPoint[0], $startingPoint[1] + 1],
        };

        if (($data[$nextPoint[0]][$nextPoint[1]] ?? null) != $nextValue) {
            return true;
        }

        if ($nextValue == $maxValue) {
            $trailEndPoints[] = $nextPoint;
            return false;
        }

        foreach (Direction::cases() as $newDirection) {
            $this->findNext($data, $nextPoint, $newDirection, $nextValue + 1, $maxValue, $trailEndPoints);
        }

        return true;
    }
}
