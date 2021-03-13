<?php

namespace App\Http\Controllers\Line;

use App\Seagon\Quotation;
use App\Seagon\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\Laravel\Facade\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

/**
 * @see https://github.com/line/line-bot-sdk-php
 */
class Messaging
{
    public function __invoke(Request $request, Slot $slot, Quotation $quotation)
    {
        Log::debug('Request content: ' . json_encode($request->all()));

        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if (preg_match('/師公！/', $text)) {
            $textMessageBuilder = new TextMessageBuilder("想聽師公講什麼嗎？請輸入下面關鍵字讓師公來講講幹話\n\n1. 師公語錄\n2. 師公第一人提出");

            $response = LINEBot::replyMessage($replyToken, $textMessageBuilder);
        }

        if ('師公語錄' === $text) {
            $textMessageBuilder = new TextMessageBuilder($quotation->random());

            $response = LINEBot::replyMessage($replyToken, $textMessageBuilder);
        }

        if ('師公第一人提出' === $text) {
            $textMessageBuilder = new TextMessageBuilder($slot->random());

            $response = LINEBot::replyMessage($replyToken, $textMessageBuilder);
        }

        if (isset($response) && !$response->isSucceeded()) {
            Log::error('LINE return error: ' . $response->getRawBody());
        }

        return response()->noContent();
    }
}
