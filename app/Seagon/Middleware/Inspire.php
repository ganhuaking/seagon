<?php

namespace App\Seagon\Middleware;

use App\Seagon\Inspire as InspireModel;
use App\Seagon\Shemale as ShemaleModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Inspire
{
    private InspireModel $inspire;

    private ShemaleModel $shemale;

    public function __construct(InspireModel $inspire, ShemaleModel $shemale)
    {
        $this->inspire = $inspire;
        $this->shemale = $shemale;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));

        if ('師公情話' === $text) {
            Log::debug('Inspire handled');

            return $this->buildMessageBuilder($this->inspire->random(), $this->shemale->random());
        }

        if (Str::startsWith($text, '師公情話給')) {
            Log::debug('Inspire to handled');

            $replace = Str::replace('師公情話給', '', $text);

            return $this->buildMessageBuilder($this->inspire->random(), $replace);
        }

        if (preg_match('/^(\d+)師公情話給(.*)/', $text, $match)) {
            Log::debug('Inspire to handled');

            $texts = $this->inspire->get((int)$match[1]);

            if (null !== $texts) {
                return $this->buildMessageBuilder($texts, $match[2]);
            }
        }

        return $next($request);
    }

    private function buildMessageBuilder(string | array $texts, string $target): TextMessageBuilder
    {
        if (is_string($texts)) {
            $texts = [$texts];
        }

        $texts = array_map(
            fn($text) => str_replace('%%user%%', '@' . $target, $text),
            $texts,
        );

        return new TextMessageBuilder(...$texts);
    }
}
