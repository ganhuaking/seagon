<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;

class Image
{
    private const IMAGE_MAPPING = [
        '錢從天降' => '000001',
        '文章有寫' => '000002',
        '元氣十足' => '000003',
    ];

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));

        $imageId = Collection::make(self::IMAGE_MAPPING)->first(
            fn($_, $key) => str_contains($text, $key),
        );

        if (null === $imageId) {
            return $next($request);
        }

        Log::info('Image handled');

        return new ImageMessageBuilder(
            "https://seagon.shang-chuan.com/images/$imageId.jpg",
            "https://seagon.shang-chuan.com/images/{$imageId}_thumb.jpg",
        );
    }
}
