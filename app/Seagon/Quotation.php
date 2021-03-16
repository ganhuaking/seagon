<?php

namespace App\Seagon;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Quotation
{
    /**
     * @var Collection
     */
    private $list;
    /**
     * @var Slot
     */
    private $slot;

    public function __construct(Filesystem $storage, Slot $slot)
    {
        $this->list = collect(
            json_decode($storage->get('quotations.json'), true)
        );

        $this->slot = $slot;
    }

    public function get(int $index): array
    {
        return $this->decorator($this->list->get($index));
    }

    public function random(): array
    {
        return $this->decorator($this->list->random());
    }

    protected function decorator(array $origin): array
    {
        if ($count = preg_match_all('/%%slot%%/', $origin['text'])) {
            for ($i = 0; $i <= $count; $i++) {
                $origin['text'] = Str::replaceFirst('%%slot%%', $this->slot->random(), $origin['text']);
            }
        }

        return $origin;
    }
}
