<?php

namespace App\Dto\Partial;

final class MigrateIdMapDto
{
    private int $new;
    private int $old;

    public function getNew(): int
    {
        return $this->new;
    }

    public function getOld(): int
    {
        return $this->old;
    }

    public function __construct(int $new, int $old)
    {
        $this->new = $new;
        $this->old = $old;
    }
}
