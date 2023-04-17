<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;

class Image
{
    private LINEBot $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if (str_contains($text, '錢從天降')) {
            Log::info('Image handled');

            $imageMessageBuilder = new ImageMessageBuilder(
                'https://seagon.shang-chuan.com/images/000001.jpg',
                'https://seagon.shang-chuan.com/images/000001_thumb.jpg',
            );

            return $this->bot->replyMessage($replyToken, $imageMessageBuilder);
        }

        return $next($request);
    }
}
