<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Reply extends Command
{
    protected $signature = 'reply {token} {text}';

    public function handle(LINEBot $bot): int
    {
        $response = $bot->replyMessage(
            $this->argument('token'),
            new TextMessageBuilder($this->argument('text')),
        );

        $this->info('Status code: ' . $response->getHTTPStatus());
        $this->info('body: ' . $response->getRawBody());

        return 0;
    }
}
