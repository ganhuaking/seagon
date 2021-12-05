<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Theory
{
    /**
     * @var LINEBot
     */
    private $bot;

    /**
     * @var Filesystem
     */
    private $storage;

    public function __construct(LINEBot $bot, Filesystem $storage)
    {
        $this->bot = $bot;
        $this->storage = $storage;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ('師公專業' === $text) {
            Log::debug('Theory handled');

            $slot = json_decode($this->storage->get('slot.json'), true);

            $textMessageBuilder = new TextMessageBuilder(collect($slot['first_round'])->merge($slot['second_round'])->join(' '));

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
