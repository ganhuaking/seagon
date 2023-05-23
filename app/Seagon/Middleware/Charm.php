<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

/**
 * 師公魅力
 */
class Charm
{
    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));

        if ('師公魅力' === $text) {
            Log::debug('Charm handled');

            $str = '能力強/懂投資理財/會運動/會音樂/會看藝術/熱愛閱讀/有胸懷/参與愛心/非常頂級的好男生/重要的是又高又帥';

            return [
                new TextMessageBuilder($str)
            ];
        }

        return $next($request);
    }
}
