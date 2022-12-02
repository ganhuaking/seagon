<?php

namespace App\Seagon\Middleware;

use App\Seagon\Inspire as InspireModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Inspire
{
    private LINEBot $bot;

    private InspireModel $inspire;

    public function __construct(LINEBot $bot, InspireModel $inspire)
    {
        $this->bot = $bot;
        $this->inspire = $inspire;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $events = $request->all();

        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ('師公情話' === $text) {
            Log::debug('Inspire handled');

            $text = str_replace('%%user%%', '@Ruby', $this->inspire->random());

            $textMessageBuilder = new TextMessageBuilder($text);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        if (Str::startsWith($text, '師公情話給')) {
            Log::debug('Inspire to handled');

            $text = str_replace('%%user%%', '@' . Str::replace('師公情話給', '', $text), $this->inspire->random());

            $textMessageBuilder = new TextMessageBuilder($text);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
