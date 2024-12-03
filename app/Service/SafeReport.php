<?php

namespace App\Service;

use Illuminate\Support\Collection;

class SafeReport
{
    public const int MIN_DIFFERENCE = 1;
    public const int MAX_DIFFERENCE = 3;

    public const string UP = 'up';
    public const string DOWN = 'down';

    public ?string $direction = null;

    public function compare(Collection $report): bool
    {
        $direction = null;
        $previous = $report->shift();

        while ($report->count() > 0) {
            $current = $report->shift();

            if (!$this->compareDifferences($previous, $current, $direction)) {
                return false;
            }

            $previous = $current;
        }

        return true;
    }

    public function compareWithCorrection(Collection $report): bool
    {
        // Check if the report is already correct
        if ($this->compare($report->collect())) {
            return true;
        }

        // Check if removing one element makes the report correct
        for ($i = 0; $i < $report->count(); $i++) {
            if ($this->compare($report->collect()->forget($i))) {
                return true;
            }
        }

        return false;
    }

    protected function compareDifferences($previous, $current, &$direction): bool
    {
        $difference = $previous - $current;
        $currentDirection = $difference < 0 ? self::DOWN : self::UP;

        if ($direction === null) {
            $direction = $currentDirection;
        }

        if (
            $direction !== $currentDirection ||
            abs($difference) < self::MIN_DIFFERENCE ||
            abs($difference) > self::MAX_DIFFERENCE)
        {
            return false;
        }


        return true;
    }
}
