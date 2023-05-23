<?php

namespace App\Seagon;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Inspire
{
    private Collection $list;

    public function __construct(Filesystem $storage)
    {
        $this->list = collect(
            json_decode($storage->get('inspire.json'), true)
        );
    }

    public function random(): mixed
    {
        return $this->list->random();
    }
}
