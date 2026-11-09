<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Waiting = 'waiting';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In progress',
            self::Waiting => 'Waiting',
            self::Resolved => 'Resolved',
        };
    }

    /** @return list<self> */
    public function transitions(): array
    {
        return match ($this) {
            self::Open => [self::InProgress, self::Waiting],
            self::InProgress => [self::Waiting, self::Resolved],
            self::Waiting => [self::InProgress, self::Resolved],
            self::Resolved => [self::Open],
        };
    }
}
