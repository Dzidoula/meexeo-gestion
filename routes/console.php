<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sans ceci, une réservation faite sur residencetouvalem.com restait invisible
// dans le tableau de bord centralisé jusqu'à ce que quelqu'un relance
// l'import à la main — constaté en production le 4 octobre, deux jours
// de décalage. L'import est un updateOrCreate par identifiant externe,
// donc rejouable sans risque de doublon.
Schedule::command('touvalem:import-hotel-data')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();
