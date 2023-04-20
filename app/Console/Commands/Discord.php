<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Discord extends Command
{
    protected $signature = 'discord {text}';

    public function handle(): int
    {
        Log::notice($this->argument('text'));

        return 0;
    }
}
