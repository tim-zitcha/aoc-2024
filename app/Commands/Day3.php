<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Day3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code - Day 3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Day 3 - Part 1');

        $result = $this->processFileRegex('/(mul\([0-9]+,[0-9]+\))/')
            ->reduce($this->multiplyStringReducer(...), 0);

        $this->info("Result: $result");

        $this->info('Day 3 - Part 2');

        $do = true;
        $result = $this->processFileRegex('/(mul\([0-9]+,[0-9]+\))|(do\(\))|(don\'t\(\))/')
            ->filter(function ($match) use (&$do) {
                if ($match === 'do()') {
                    $do = true;
                    return false;
                }

                if ($match === 'don\'t()') {
                    $do = false;
                    return false;
                }
                return $do;
            })
            ->reduce($this->multiplyStringReducer(...), 0);

        $this->info("Result: $result");
    }

    protected function processFileRegex(string $pattern): Collection
    {
        $file = file_get_contents(__DIR__ . '/../Data/day3.txt');
        preg_match_all($pattern, $file, $matches);

        return collect($matches[0]);
    }

    protected function multiplyStringReducer(int $carry, string $string): int
    {
        $values = Str::of($string)->replace(['mul(', ')'], '')
            ->explode(',');

        $carry += $values[0] * $values[1];

        return $carry;
    }
}
