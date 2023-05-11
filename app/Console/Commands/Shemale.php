<?php

namespace App\Console\Commands;

use App\Seagon\Shemale as ShemaleModel;
use Illuminate\Console\Command;

class Shemale extends Command
{
    protected $signature = 'shemale';

    public function handle(ShemaleModel $shemale): int
    {
        $this->line($shemale->random());

        return 0;
    }
}
