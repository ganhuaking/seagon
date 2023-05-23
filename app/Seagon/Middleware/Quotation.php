<?php

namespace App\Seagon\Middleware;

use App\Seagon\Quotation as QuotationModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Quotation
{
    /**
     * @var QuotationModel
     */
    private QuotationModel $quotation;

    public function __construct(QuotationModel $quotation)
    {
        $this->quotation = $quotation;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $events = $request->all();

        $text = trim($request->input('events.0.message.text'));

        if ('師公語錄' === $text) {
            Log::debug('Quotation handled');

            return [
                new TextMessageBuilder($this->quotation->random($events)['text']),
            ];
        }

        if ('師公語錄ref' === mb_strtolower($text)) {
            Log::debug('Quotation ref handled');

            $random = $this->quotation->random($events);

            return [
                new TextMessageBuilder(
                    $random['text'] . "\n\nRef: " . $random['ref']
                ),
            ];
        }

        if (preg_match('/^師公語錄(\d+)$/', $text, $match)) {
            Log::debug('Quotation specify handled');

            return [
                new TextMessageBuilder($this->quotation->get($events, $match[1])['text']),
            ];
        }

        if (preg_match('/^師公語錄(\d+)ref$/', $text, $match)) {
            Log::debug('Quotation specify handled');

            $get = $this->quotation->get($events, $match[1]);

            return [
                new TextMessageBuilder(
                    $get['text'] . "\n\nRef: " . $get['ref']
                ),
            ];
        }

        if (preg_match('/^師公語錄話(.*)$/', $text, $match)) {
            Log::debug('Quotation search handled');

            return [
                new TextMessageBuilder($this->quotation->find($events, $match[1])['text']),
            ];
        }

        return $next($request);
    }
}
