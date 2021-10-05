<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Stock
{
    /**
     * @var LINEBot
     */
    private $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if ($this->keyword($text)) {
            Log::debug('Stock handled');

            if ($this->threshold()) {
                $stockText = '舉例來說，我就買了中信金，現在每年為我創造額外的股息收入來源';
            } else {
                $stockText = '舉例來說，我就買了富邦金，現在每年為我創造額外的股息收入來源';
            }

            $textMessageBuilder = new TextMessageBuilder($stockText . "\n" . '本金...本金你不會自己想辦法賺嗎？
你不會自己跟銀行借錢嗎？
你不會想辦法說服別人投資你或是請你操盤？
你不會試者說服家人投資你之類或是家人借錢給你之類？
方法這麼多你有沒有去嘗試過？
問這麼多? 所以勒 然後勒?
說穿還是看戲，看熱鬧而已...
因為你只想找貪心且最便宜的方法去做!
就算你有錢，你也沒那個屁股敢這樣玩啦，你敢帶種果斷執行？');

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }

    /**
     * @param string $text
     * @return bool
     */
    private function keyword(string $text): bool
    {
        return preg_match('/師公/', $text) && preg_match('/股票/', $text);
    }

    /**
     * 30%
     *
     * @return bool
     */
    private function threshold(): bool
    {
        $rand = rand(0, 100);

        Log::debug('Secret threshold: ' . $rand);

        return $rand < 90;
    }
}
