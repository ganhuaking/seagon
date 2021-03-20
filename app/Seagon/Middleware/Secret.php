<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Secret
{
    /**
     * @var LINEBot
     */
    private $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ($this->keyword($text) && $this->threshold()) {
            Log::debug('Secret handled');

            $textMessageBuilder = new TextMessageBuilder('秘密');

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }

    /**
     * @param string $text
     * @return bool
     */
    private function keyword(string $text): bool
    {
        return preg_match('/師公/', $text) && preg_match('/投資/', $text);
    }

    /**
     * 30%
     *
     * @return bool
     */
    private function threshold(): bool
    {
        $rand = rand(0, 100);

        Log::debug('Secret threshold: ' . $rand);

        return $rand < 90;
    }
}
