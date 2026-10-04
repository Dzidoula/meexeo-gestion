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
// La sortie est conservée : updated_at ne bouge pas quand la source n'a pas
// changé (Eloquent n'écrit rien sur un modèle inchangé), donc il ne prouve
// pas qu'une exécution a eu lieu. Ce journal, si.
Schedule::command('touvalem:import-hotel-data')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/touvalem-sync.log'));

// Rappels de loyer du portail locataire (J-5 et jour J). 08:00 UTC = 08:00 à
// Abidjan, le fuseau de l'application étant UTC. Le journal garde une trace de
// chaque exécution, comme pour la synchronisation Touvalem.
Schedule::command('portal:send-rent-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/portal-rent-reminders.log'));
