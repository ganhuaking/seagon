<?php

namespace App\Http\Controllers\Line;

use App\Seagon\Middleware\Inspire;
use App\Seagon\Middleware\Quotation;
use App\Seagon\Middleware\Secret;
use App\Seagon\Middleware\Slot;
use App\Seagon\Middleware\Stock;
use App\Seagon\Middleware\Talk;
use App\Seagon\Middleware\TalkNathan;
use App\Seagon\Middleware\Theory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

/**
 * @see https://github.com/line/line-bot-sdk-php
 */
class Messaging
{
    public function __invoke(Request $request, LINEBot $bot): Response
    {
        $seagonId = env('SEAGON_ID');

        // "groupId":"C67fc7032fb5c1cb77e276f3582710637"
        Log::debug('Request content: ' . json_encode($request->all()));

        // 開關沒開的話，就回 204
        if (!env('LINE_MESSAGEING_TOGGLE')) {
            return response()->noContent();
        }

        // 確認白名單的群組
        if ($list = env('LINE_MESSAGING_GROUP_ALLOW_LIST')) {
            $notFound = true;

            foreach (explode(',', $list) as $group) {
                if ($request->input('events.0.source.groupId') === $group) {
                    $notFound = false;
                }
            }

            // 不在白名單
            if ($notFound) {
                // UserID == Seagon
                if ($seagonId === $request->input('events.0.source.userId')) {
                    $middleware = [
                        TalkNathan::class,
                    ];

                    (new Pipeline(app()))
                        ->send($request)
                        ->through($middleware)
                        ->then(function () {
                            return null;
                        });
                }

                return response()->noContent();
            }
        }

        $middleware = [
            Talk::class,
            Quotation::class,
            Inspire::class,
            Slot::class,
            Theory::class,
            Stock::class,
            Secret::class,
        ];

        $response = (new Pipeline(app()))
            ->send($request)
            ->through($middleware)
            ->then(function (Request $request) use ($bot) {
                $text = trim($request->input('events.0.message.text'));
                $replyToken = $request->input('events.0.replyToken');

                if (str_contains($text, '師公！')) {
                    Log::debug('Menu handled');

                    $textMessageBuilder = new TextMessageBuilder("想聽師公講什麼嗎？請輸入下面關鍵字讓師公來講講幹話\n\n1. 師公語錄\n2. 師公語錄話XX，XX關鍵字任你帶\n3. 師公第一人提出\n4. 師公專業\n5. 師公情話 / 師公情話給xx\n6. 師公聊聊XX");

                    return $bot->replyMessage($replyToken, $textMessageBuilder);
                }
            });

        if (isset($response) && !$response->isSucceeded()) {
            Log::error('LINE return error: ' . $response->getRawBody());
        }

        return response()->noContent();
    }
}
