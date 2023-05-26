<?php

namespace App\Http\Controllers\Line;

use App\Seagon\Middleware\AntiFraud;
use App\Seagon\Middleware\Charm;
use App\Seagon\Middleware\ChatGPT;
use App\Seagon\Middleware\Image;
use App\Seagon\Middleware\Inspire;
use App\Seagon\Middleware\Quotation;
use App\Seagon\Middleware\Secret;
use App\Seagon\Middleware\Slot;
use App\Seagon\Middleware\Stock;
use App\Seagon\Middleware\Talk;
use App\Seagon\Middleware\Theory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder;
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
        Log::debug('Request content: ' . json_encode($request->all(), JSON_UNESCAPED_UNICODE));

        if ($request->has('events.0.message.text')) {
            $this->textLog($request, $bot);
        }

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
                        ChatGPT::class,
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
            AntiFraud::class,
            Image::class,
            Charm::class,
            Talk::class,
            Quotation::class,
            Inspire::class,
            Slot::class,
            Theory::class,
            Stock::class,
            Secret::class,
        ];

        /** @var MessageBuilder $messageBuilder */
        $messageBuilder = (new Pipeline(app()))
            ->send($request)
            ->through($middleware)
            ->then(function (Request $request) {
                $text = trim($request->input('events.0.message.text'));

                if (str_contains($text, '師公！')) {
                    Log::debug('Menu handled');

                    return new TextMessageBuilder("想聽師公講什麼嗎？請輸入下面關鍵字讓師公來講講幹話\n\n1. 師公語錄\n2. 師公語錄話XX，XX關鍵字任你帶\n3. 師公第一人提出\n4. 師公專業\n5. 師公情話 / 師公情話給xx\n6. 師公聊聊XX");
                }
            });

        if (null === $messageBuilder) {
            return response()->noContent();
        }

        $response = $bot->replyMessage(
            $request->input('events.0.replyToken'),
            $messageBuilder,
        );

        if (isset($response) && !$response->isSucceeded()) {
            Log::error('LINE return error: ' . $response->getRawBody());
        }

        return response()->noContent();
    }

    private function textLog(Request $request, LINEBot $bot): void
    {
        $seagonId = env('SEAGON_ID');

        $groupId = $request->input('events.0.source.groupId');

        $ignoreGroup = [
            'C5273a599868b4423212d07c11854e3bf',
        ];

        if (in_array($groupId, $ignoreGroup, true)) {
            return;
        }

        $group = match ($groupId) {
            'C67fc7032fb5c1cb77e276f3582710637' => 'Fork',
            'Cee77733c527ac65600a34e6d5c487517' => 'Origin',
            default => $groupId,
        };

        $userId = $request->input('events.0.source.userId');
        $user = match ($userId) {
            $seagonId => 'Seagon',
            default => $this->getUser($userId, $bot),
        };

        $text = $request->input('events.0.message.text');

        Log::notice("$group - $user : $text");
    }

    private function getUser(string $userId, LINEBot $bot): string
    {
        $profile = $bot->getProfile($userId);

        if (404 !== $profile->getHTTPStatus()) {
            return $profile->getJSONDecodedBody()['displayName'];
        } else {
            return $userId;
        }
    }
}
