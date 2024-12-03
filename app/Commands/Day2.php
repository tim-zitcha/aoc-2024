<?php

namespace App\Commands;

use App\Service\SafeReport;
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
    public function handle(): void
    {
        $this->info('Day 2 - Part 1');

        $safeReports = $this->processFile((new SafeReport())->compare(...));
        $this->info("Total safe reports: {$safeReports->count()}");

        $this->info('Day 2 - Part 2');

        $safeReportsWithCorrection = $this->processFile((new SafeReport())->compareWithCorrection(...));

        $this->info("Total safe reports with correction: {$safeReportsWithCorrection->count()}");
    }

    protected function processFile($callback): Collection
    {
        return Str::of(file_get_contents(__DIR__ . '/../Data/day2.txt'))
            ->explode("\n")
            ->filter(fn($line) => !empty($line))
            ->map(fn($number) => Str::of($number)
                ->explode(' ')
                ->map(fn($number) => (int)$number)
            )->filter($callback);
    }
}
