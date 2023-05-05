<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;

class Image
{
    private LINEBot $bot;

    private const IMAGE_MAPPING = [
        '錢從天降' => '000001',
        '文章有寫' => '000002',
        '元氣十足' => '000003',
        '元來這就是愛情' => '000004',
    ];

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        $imageId = Collection::make(self::IMAGE_MAPPING)->first(
            fn($_, $key) => str_contains($text, $key),
        );

        if (null === $imageId) {
            return $next($request);
        }

        Log::info('Image handled');

        $imageMessageBuilder = new ImageMessageBuilder(
            "https://seagon.shang-chuan.com/images/$imageId.jpg",
            "https://seagon.shang-chuan.com/images/{$imageId}_thumb.jpg",
        );

        return $this->bot->replyMessage($replyToken, $imageMessageBuilder);
    }
}