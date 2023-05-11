<?php

namespace App\Seagon;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Shemale 
{
    private Collection $list;

    public function __construct(Filesystem $storage)
    {
        $this->list = collect(
            json_decode($storage->get('shemale.json'), true)
        );
    }

    public function random(): string
    {
        return $this->list->random();
    }
}
