<?php

namespace App\Seagon\Middleware;

use App\Seagon\Inspire as InspireModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Throwable;

class Talk
{
    private LINEBot $bot;

    public function __construct(LINEBot $bot, InspireModel $inspire)
    {
        $this->bot = $bot;
        $this->inspire = $inspire;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        if (Str::startsWith($text, '師公聊聊')) {
            Log::debug('Talk handled');

            try {
                $response = Http::withToken(config('openai.api_key'))
                    ->timeout(60)
                    ->post('https://api.openai.com/v1/completions', [
                        'model' => 'text-davinci-003',
                        'prompt' => Str::replace('師公聊聊', '', $text),
                        'max_tokens' => 2000,
                    ]);

                $reply = $response->json('choices.0.text');
            } catch (Throwable $e) {
                $reply = '師公聊聊壞了：' . $e->getMessage();
            }

            if (empty($reply)) {
                $reply = '師公聊聊壞了';
            }

            $textMessageBuilder = new TextMessageBuilder($reply);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
