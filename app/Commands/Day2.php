<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Day2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code Day 2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Day 2');
        $this->info('Part 1');

        $reports = Str::of(file_get_contents(__DIR__ . '/../Data/day2.txt'))
            ->explode("\n")
            ->map(fn($number) => Str::of($number)
                ->explode(' ')
                ->map(fn($number) => (int)$number)
            );

        $reports->reduce(function ($carry, $report) {
            $carry += $this->isReportSafe($report) ? 1 : 0;
            return $carry;
        }, 0);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    protected function isReportSafe(Collection $report): bool
    {

    }
}
