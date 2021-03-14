<?php

namespace App\Seagon;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Quotation
{
    /**
     * @var Collection
     */
    private $list;

    public function __construct(Filesystem $storage)
    {
        $this->list = collect(
            json_decode($storage->get('quotations.json'), true)
        );
    }

    public function random(): string
    {
        return $this->list->random()['text'];
    }
}
