<?php

namespace App\Seagon\Middleware;

use App\Seagon\Inspire as InspireModel;
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

class TalkNathan
{
    private LINEBot $bot;

    private Repository $cache;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
        $this->cache = Cache::store();
    }

    public function __invoke(Request $request, Closure $next)
    {
        $percentage = 75;

        if ($this->cache->has('nathan_said')) {
            $percentage = 100;
        }

        if (!Random::threshold($percentage)) {
            return $next($request);
        }

        $text = trim($request->input('events.0.message.text'));
        $replyToken = $request->input('events.0.replyToken');

        $luckyUser = [
            '@雷N',
            '@Gson',
        ];

        if (Str::contains($text, '@雷N')) {
            Log::debug('Talk Lucky User handled');

            $t = trim(Str::replace('@雷N', '', $text));

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
            } catch (Throwable $e) {
                $reply = '@雷N 壞了：' . $e->getMessage();
            }

            if (empty($reply)) {
                $reply = '@雷N 壞了';
            }

            $textMessageBuilder = new TextMessageBuilder($reply);

            return $this->bot->replyMessage($replyToken, $textMessageBuilder);
        }

        return $next($request);
    }
}
