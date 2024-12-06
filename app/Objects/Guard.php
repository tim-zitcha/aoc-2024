<?php

namespace App\Objects;

class Guard
{
    public const string UP = '^';
    public const string DOWN = 'v';
    public const string LEFT = '<';
    public const string RIGHT = '>';

    public int $x = 0;
    public int $y = 0;
    public string $direction = self::UP;

    public array $history = [];

    public function __construct(int $x, int $y, string $direction)
    {
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;

        $this->history[] = [$x, $y, $direction];
    }

    public function nextCoordinate(): array
    {
        $x = $this->x;
        $y = $this->y;
        switch ($this->direction) {
            case self::UP:
                $x--;
                break;
            case self::DOWN:
                $x++;
                break;
            case self::LEFT:
                $y--;
                break;
            case self::RIGHT:
                $y++;
                break;
        }
        return [$x, $y];
    }

    public function step(): void
    {
        $this->history[] = [$this->x, $this->y, $this->direction];
        [$x, $y] = $this->nextCoordinate();
        $this->x = $x;
        $this->y = $y;
    }

    public function turnLeft(): void
    {
        $this->history[] = [$this->x, $this->y, $this->direction];

        switch ($this->direction) {
            case self::UP:
                $this->direction = self::RIGHT;
                break;
            case self::DOWN:
                $this->direction = self::LEFT;
                break;
            case self::LEFT:
                $this->direction = self::UP;
                break;
            case self::RIGHT:
                $this->direction = self::DOWN;
                break;
        }
    }

    public function inHistory(mixed $x, mixed $y, mixed $direction): bool
    {
        return in_array([$x, $y, $direction], $this->history);
    }
}
