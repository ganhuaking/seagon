<?php

namespace App\Console;

use App\Console\Commands\Discord;
use App\Console\Commands\Reply;
use App\Console\Commands\Shemale;
use App\Console\Commands\Talk;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Discord::class,
        Reply::class,
        Shemale::class,
        Talk::class,
    ];
}
