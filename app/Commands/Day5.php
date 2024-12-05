<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Day5 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Day 5 - Part 1');
        [$rules, $updates] = $this->processFile();

        $rules = $rules->map(fn($rule) => Str::of($rule)->explode('|'));
        $updates = $updates->map(fn($update) => Str::of($update)->explode(','));

        $valid = $updates->filter(fn(Collection $update) => $this->checkValid($update, $rules));
        $total = $this->calculateTotal($valid);
        $this->info("Total: $total");

        $this->info('Day 5 - Part 2');

        $invalid = $updates->filter(fn(Collection $update) => !$this->checkValid($update, $rules))
            ->map(fn(Collection $update) => $this->fixInvalid($update, $rules));

        $total = $this->calculateTotal($invalid);

        $this->info("Total: $total");

    }

    /**
     * Parse the rule
     *
     * @param string $rule
     * @return array{Collection<int, string>, Collection<int, string>}
     */
    protected function processFile(): array
    {
        $lines = collect(explode("\n", file_get_contents(__DIR__ . '/../Data/day5.txt')));

        $updates = $lines->splice($lines->search(fn($line) => empty($line)))
            ->reject(fn($line) => empty($line));

        return [$lines, $updates];
    }

    protected function checkValid(Collection $update, Collection $rules): bool
    {
        return $update->diffAssoc($this->fixInvalid($update, $rules))->isEmpty();
    }

    protected function calculateTotal(Collection $values): int
    {
        return $values->reduce(function ($carry, $item) {
            $middle = (int)ceil($item->count() / 2) - 1;
            return $carry + $item[$middle];
        }, 0);
    }

    private function fixInvalid(Collection $update, Collection $rules): Collection
    {
        return $update->sort(function ($seat, $next) use ($rules, $update) {
            $correct = $rules->filter(fn($rule) => $rule[0] === $seat && $rule[1] === $next);
            return $correct->isNotEmpty() ? -1 : 1;
        })->values();
    }
}
