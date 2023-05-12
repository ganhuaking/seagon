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

        $shemale = $this->shemale->random();

        if ('師公情話' === $text) {
            Log::debug('Inspire handled');

            $text = str_replace('%%user%%', '@' . $shemale, $this->inspire->random());

            return new TextMessageBuilder($text);
        }

        if (Str::startsWith($text, '師公情話給')) {
            Log::debug('Inspire to handled');

            $text = str_replace('%%user%%', '@' . Str::replace('師公情話給', '', $text), $this->inspire->random());

            return new TextMessageBuilder($text);
        }

        return $next($request);
    }
}
