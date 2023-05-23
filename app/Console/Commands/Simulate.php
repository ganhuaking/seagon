<?php

namespace App\Console\Commands;

use App\Seagon\Middleware\Charm;
use App\Seagon\Middleware\Image;
use App\Seagon\Middleware\Inspire;
use App\Seagon\Middleware\Quotation;
use App\Seagon\Middleware\Secret;
use App\Seagon\Middleware\Slot;
use App\Seagon\Middleware\Stock;
use App\Seagon\Middleware\Talk;
use App\Seagon\Middleware\Theory;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class Simulate extends Command
{
    protected $signature = 'simulate {text} {--user=Tester}';

    protected $description = '模擬用';

    public function handle(): int
    {
        $text = $this->argument('text');
        $user = $this->option('user');

        $middleware = [
            Image::class,
            Charm::class,
            Talk::class,
            Quotation::class,
            Inspire::class,
            Slot::class,
            Theory::class,
            Stock::class,
            Secret::class,
        ];

        // 製造假的 Request 物件
        $request = new Request(query: [
            'events' => [
                [
                    'message' => [
                        'text' => $text,
                    ],
                ],
            ],
        ]);

        /** @var null|MessageBuilder[] $messageBuilders */
        $messageBuilders = (new Pipeline(app()))
            ->send($request)
            ->through($middleware)
            ->then(function (Request $request) {
                $text = trim($request->input('events.0.message.text'));

                if (str_contains($text, '師公！')) {
                    Log::debug('Menu handled');

                    return [
                        new TextMessageBuilder("想聽師公講什麼嗎？請輸入下面關鍵字讓師公來講講幹話\n\n1. 師公語錄\n2. 師公語錄話XX，XX關鍵字任你帶\n3. 師公第一人提出\n4. 師公專業\n5. 師公情話 / 師公情話給xx\n6. 師公聊聊XX"),
                    ];
                }
            });

        if (empty($messageBuilders)) {
            $this->error('NO MATCH');
            return Command::FAILURE;
        }

        foreach ($messageBuilders as $messageBuilder) {
            $buildMessage = $messageBuilder->buildMessage();

            if ($buildMessage[0]['type'] !== 'text') {
                $this->error('TYPE IS NOT VALID: ' . $buildMessage[0]['type']);
            } else {
                $this->line($buildMessage[0]['text'],);
            }
        }

        return Command::SUCCESS;
    }
}
