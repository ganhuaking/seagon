<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Talk extends Command
{
    protected $signature = 'talk {prompt}';

    public function handle(): int
    {
        $prompt = <<<EOL
Human:{$this->argument('prompt')}
AI:
EOL;

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

        $this->info(trim($response->json('choices.0.text')));

        return 0;
    }
}
