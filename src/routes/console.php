<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// llamr el comando deploy
Artisan::command('deploy', function () {
    $this->comment('Deploying...');
    $this->call('migrate');
    $this->call('db:seed');
    $this->comment('Deployed!');
})->purpose('Deploy the application');
