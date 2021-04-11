<?php

namespace App\Seagon;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LINE\LINEBot;

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
    /**
     * @var LINEBot
     */
    private $line;

    public function __construct(LINEBot $line, Filesystem $storage, Slot $slot)
    {
        $this->line = $line;

        $this->list = collect(
            json_decode($storage->get('quotations.json'), true)
        );

        $this->slot = $slot;
    }

    public function get(array $events, int $index): array
    {
        return $this->decorator($events, $this->list->get($index));
    }

    public function find(array $events, string $keyword): array
    {
        return $this->decorator($events, $this->list->filter(function ($item) use ($keyword) {
            return Str::contains($item['text'], $keyword);
        })->random());
    }

    public function random(array $events): array
    {
        return $this->decorator($events, $this->list->random());
    }

    protected function decorator(array $events, array $origin): array
    {
        if ($count = preg_match_all('/%%slot%%/', $origin['text'])) {
            for ($i = 0; $i <= $count; $i++) {
                $origin['text'] = Str::replaceFirst('%%slot%%', $this->slot->random(), $origin['text']);
            }
        }

        if (preg_match('/%%user%%/', $origin['text'])) {
            $profile = $this->line->getProfile($events['events'][0]['source']['userId']);

            if (404 !== $profile->getHTTPStatus()) {
                $displayName = $profile->getJSONDecodedBody()['displayName'];
            } else {
                $displayName = '';
            }

            $origin['text'] = str_replace('%%user%%', $displayName, $origin['text']);
        }

        return $origin;
    }
}
