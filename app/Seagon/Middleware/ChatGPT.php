<?php

namespace App\Seagon\Middleware;

use App\Seagon\Random;
use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Throwable;

class ChatGPT
{
    private LINEBot $bot;

    private int $percentage;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
        $this->percentage = 75;
    }

    public function __invoke(Request $request, Closure $next)
    {
        if (!Random::threshold($this->percentage)) {
            return $next($request);
        }

        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        $luckyUser = [
            '@雷N',
            '@Eileen',
            '@Gson',
            '@James',
            '@SU',
            '@Yang',
        ];

        if (Str::contains($text, $luckyUser)) {
            Log::debug('Talk Lucky User handled');

            $t = $text;

            foreach ($luckyUser as $user) {
                $t = trim(Str::replace($user, '', $t));
            }

            $prompt = <<<EOL
Human:{$t}
AI:
EOL;

            try {
                $response = Http::withToken(config('openai.api_key'))
                    ->timeout(60)
                    ->post('https://api.openai.com/v1/completions', [
                        'model' => 'text-davinci-003',
                        'prompt' => $prompt,
                        'temperature' => 0.7,
                        'max_tokens' => 2000,
                        'top_p' => 1,
                        'frequency_penalty' => 0.0,
                        'presence_penalty' => 0.6,
                        'stop' => ['Human:', 'AI:'],
                    ]);

                $reply = trim($response->json('choices.0.text'));
            } catch (Throwable) {
                $reply = '你的問題';
            }

            if (empty($reply)) {
                $reply = '你的問題';
            }

            $textMessageBuilder = new TextMessageBuilder($reply);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
