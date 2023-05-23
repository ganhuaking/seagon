<?php

namespace App\Seagon\Middleware;

use App\Seagon\Slot as SlotModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Slot
{
    /**
     * @var SlotModel
     */
    private SlotModel $slot;

    public function __construct(SlotModel $slot)
    {
        $this->slot = $slot;
    }

    public function __invoke(Request $request, Closure $next)
    {
        $text = trim($request->input('events.0.message.text'));

        if ('師公第一人提出' === $text) {
            Log::debug('Slot handled');

            return [
                new TextMessageBuilder($this->slot->random()),
            ];
        }

        return $next($request);
    }
}
