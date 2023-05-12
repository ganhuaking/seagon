<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Theory
{
    /**
     * @var Filesystem
     */
    private Filesystem $storage;

    public function __construct(Filesystem $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));

        if ('師公專業' === $text) {
            Log::debug('Theory handled');

            $slot = json_decode($this->storage->get('slot.json'), true);

            return new TextMessageBuilder(collect($slot['first_round'])->merge($slot['second_round'])->join(' '));
        }

        return $next($request);
    }
}
