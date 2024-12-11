<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ProcessFile
{
    public function processFile(string $filename, ?\Closure $mapCallback = null): \Illuminate\Support\Collection
    {
        if ($mapCallback === null) {
            $mapCallback = fn($line) => $line;
        }
        return Str::of(file_get_contents(app_path('Data/' . $filename . '.txt')))
            ->explode("\n")
            ->filter(fn($line) => $line !== '')
            ->map($mapCallback);
    }
}
