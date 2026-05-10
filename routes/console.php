<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\{Artisan, Schedule};

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());



})->purpose('Display an inspiring quote');

Schedule::command('rh:atualizar-ferias')->daily()->at('06:00')->timezone('America/Porto_Velho');
