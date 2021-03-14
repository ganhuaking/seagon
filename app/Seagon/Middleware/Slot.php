<?php

namespace App\Seagon\Middleware;

use App\Seagon\Slot as SlotModel;
use Closure;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Slot
{
    /**
     * @var LINEBot
     */
    private $bot;

    /**
     * @var SlotModel
     */
    private $slot;

    public function __construct(LINEBot $bot, SlotModel $slot)
    {
        $this->bot = $bot;
        $this->slot = $slot;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ('師公第一人提出' === $text) {
            $textMessageBuilder = new TextMessageBuilder($this->slot->random());

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
