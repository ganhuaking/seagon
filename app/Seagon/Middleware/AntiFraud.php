<?php

namespace App\Seagon\Middleware;

use App\Seagon\Inspire as InspireModel;
use App\Seagon\Shemale as ShemaleModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class AntiFraud
{
    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));

        $pattern = '/https?:\/\/\S+?(?=\p{Script=Han}|$)/iu';

        preg_match_all($pattern, $text, $matches);

        if (count($matches[0]) > 0) {
            $urls = array_map(fn($url) => $this->resolveShortUrl($url), $matches[0]);

            foreach ($urls as $url) {
                $sensitive = [
                    'u8961310',
                    'eddiechen0801',
                ];

                if (Str::contains($url, $sensitive)) {
                    return new TextMessageBuilder('騙不倒我的');
                }
            }
        }

        return $next($request);
    }

    private function resolveShortUrl(string $url): string
    {
        if(!$this->isShortUrl($url)) {
            return $url;
        }

        try {
            $location = Http::withoutRedirecting()->head($url)->header('Location');

            return $this->resolveShortUrl($location);
        } catch (\Throwable) {
            return $url;
        }
    }

    private function isShortUrl(string $url): bool
    {
        return Str::contains($url, [
            'https://tinyurl.com/',
        ]);
    }
}
