<?php

namespace App\Seagon;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Slot
{
    /**
     * @var Collection
     */
    private $first;

    /**
     * @var Collection
     */
    private $second;

    /**
     * @var Collection
     */
    private $third;

    public function __construct(Filesystem $storage)
    {
        $slot = json_decode($storage->get('slot.json'), true);

        $this->first = collect($slot['first_round']);
        $this->second = collect($slot['second_round']);
        $this->third = collect($slot['third_round']);
    }

    public function random(): string
    {
        return "{$this->first->random()}{$this->second->random()}{$this->third->random()}";
    }
}
