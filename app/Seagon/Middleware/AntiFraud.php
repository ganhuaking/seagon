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
                    '%E5%86%A0%E5%BB%B7-%E9%99%B3-8a7aa6a7',
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
        if (!$this->isShortUrl($url)) {
            return $url;
        }

        try {
            $response = Http::withoutRedirecting()
                ->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36')
                ->get($url);

            $redirect = $response->header('location');

            if (empty($redirect)) {
                $redirect = $response->header('target');
            }

            return $this->resolveShortUrl($redirect);
        } catch (\Throwable $e) {
            Log::notice('Url parse error: ' . $e->getMessage(), [$e]);

            return $url;
        }
    }

    private function isShortUrl(string $url): bool
    {
        return Str::contains($url, [
            'https://tinyurl.com/',
            'https://reurl.cc/',
        ]);
    }
}
