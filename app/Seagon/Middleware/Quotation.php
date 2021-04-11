<?php

namespace App\Seagon\Middleware;

use App\Seagon\Quotation as QuotationModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Quotation
{
    /**
     * @var LINEBot
     */
    private $bot;

    /**
     * @var QuotationModel
     */
    private $quotation;

    public function __construct(LINEBot $bot, QuotationModel $quotation)
    {
        $this->bot = $bot;
        $this->quotation = $quotation;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $events = $request->all();

        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ('師公語錄' === $text) {
            Log::debug('Quotation handled');

            $textMessageBuilder = new TextMessageBuilder($this->quotation->random($events)['text']);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        if ('師公語錄ref' === mb_strtolower($text)) {
            Log::debug('Quotation ref handled');

            $random = $this->quotation->random($events);
            $textMessageBuilder = new TextMessageBuilder(
                $random['text'] . "\n\nRef: " . $random['ref']
            );

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        if (preg_match('/^師公語錄(\d+)$/', $text, $match)) {
            Log::debug('Quotation specify handled');

            $textMessageBuilder = new TextMessageBuilder($this->quotation->get($events, $match[1])['text']);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        if (preg_match('/^師公語錄(\d+)ref$/', $text, $match)) {
            Log::debug('Quotation specify handled');

            $get = $this->quotation->get($events, $match[1]);
            $textMessageBuilder = new TextMessageBuilder(
                $get['text'] . "\n\nRef: " . $get['ref']
            );

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        if (preg_match('/^師公語錄話(.*)$/', $text, $match)) {
            Log::debug('Quotation search handled');

            $textMessageBuilder = new TextMessageBuilder($this->quotation->find($events, $match[1])['text']);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
