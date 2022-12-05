<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Talk extends Command
{
    protected $signature = 'talk {prompt}';

    public function handle(): int
    {
        $response = Http::withToken(config('openai.api_key'))
            ->timeout(60)
            ->post('https://api.openai.com/v1/completions', [
                'model' => 'text-davinci-003',
                'prompt' => $this->argument('prompt'),
                'max_tokens' => 2000,
            ]);

        $this->info($response->json('choices.0.text'));

        return 0;
    }
}
