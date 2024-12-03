<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Day1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code Day 1';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $numbers = Str::of(file_get_contents(__DIR__ . '/../Data/day1.txt'))
            ->explode("\n")
            ->map(fn($number) => Str::of($number)
                ->explode('   ')
                ->map(fn($number) => (int)$number)
            );

        $firstSet = $numbers->pluck(0)->sort()->values();
        $secondSet = $numbers->pluck(1)->sort()->values();

        $output = $firstSet->reduce(function ($carry, $number1, $key) use ($secondSet) {
            $difference = $number1 - $secondSet[$key];
            $carry += $difference < 0 ? $difference * -1 : $difference;
            return $carry;
        }, 0);

        $this->info("Total Difference: $output");

        $this->info('Part 2');

        $output = $firstSet->reduce(function ($carry, $number1) use ($secondSet) {
            $numberOfTimes = $secondSet->filter(fn($number) => $number === $number1)->count();
            $carry += $number1 * $numberOfTimes;
            return $carry;
        }, 0);

        $this->info("Total Similarity: $output");

    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
