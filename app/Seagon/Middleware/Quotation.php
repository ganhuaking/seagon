<?php

namespace App\Seagon\Middleware;

use App\Seagon\Quotation as QuotationModel;
use Closure;
use Illuminate\Http\Request;
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
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ('師公語錄' === $text) {
            $textMessageBuilder = new TextMessageBuilder($this->quotation->random());

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
