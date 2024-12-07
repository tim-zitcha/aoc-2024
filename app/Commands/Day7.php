<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use function PHPUnit\Framework\isEmpty;

class Day7 extends Command
{
    const ADD = '+';
    const MULTIPLY = '*';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:day7';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advent of Code - Day 7';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $addition = static fn(int $first, int $second): int => $first + $second;
        $multiplication = static fn(int $first, int $second): int => $first * $second;
        $concatenation = static fn(int $first, int $second): int => (int)"$first$second";

        $data = $this->processFile();

        $this->info('Day 7 - Part 1');
        $matches = $data->filter(
            fn(array $row): bool => $this->getCombinations($row['values'], [$addition, $multiplication])
                ->contains($row['result'])
        )->sum(fn($row) => $row['result']);

        $this->info("Matches: $matches");

        $this->info('Day 7 - Part 2');

        $matches = $data->filter(
            fn(array $row): bool => $this->getCombinations($row['values'], [$addition, $multiplication, $concatenation])
                ->contains($row['result'])
        )->sum(fn($row) => $row['result']);

        $this->info("Matches: $matches");
    }

    protected function processFile(): Collection
    {
        return Str::of(file_get_contents(__DIR__ . '/../Data/day7.txt'))
            ->explode("\n")
            ->filter(fn(string $line) => $line !== '')
            ->map(fn($line) => Str::of($line)->explode(':'))
            ->map(fn(Collection $parts) => [
                'result' => $parts->first(),
                'values' => Str::of($parts->last())->trim()
                    ->explode(' ')
                    ->map(fn($child) => Str::of($child)->trim()->toInteger())
            ]);
    }

    private function getCombinations(Collection $values, array $operations): Collection
    {
        return $values->reduce(function (Collection $carry, int $value) use ($operations) {
            if ($carry->isEmpty()) {
                return collect([$value]);
            }

            $returned = collect();
            foreach ($operations as $operation) {
                $returned = $carry->map(fn($carryValue) => $operation($carryValue, $value))
                    ->merge($returned);
            }
            return collect($returned);
        }, collect());
    }
}
