<?php

namespace App\Console\Commands;

use App\Services\TouvalemImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-off (re-runnable) import of Résidence Touvalem's real hotel data
 * (room_types, rooms, bookings) into this app's Hotel module, via the
 * read-only `touvalem` DB connection (see config/database.php).
 */
class ImportTouvalemHotelData extends Command
{
    protected $signature = 'touvalem:import-hotel-data';

    protected $description = "Importe les chambres, types et réservations réels de Résidence Touvalem dans le module Hôtel";

    public function handle(TouvalemImportService $service): int
    {
        $source = DB::connection('touvalem');

        $roomTypes = $source->table('room_types')->get()->map(fn ($row) => (array) $row)->all();
        $this->info(sprintf('Types de chambre trouvés : %d', count($roomTypes)));
        $service->importRoomTypes($roomTypes);

        $rooms = $source->table('rooms')->get()->map(fn ($row) => (array) $row)->all();
        $this->info(sprintf('Chambres trouvées : %d', count($rooms)));
        $service->importRooms($rooms);

        $bookings = $source->table('bookings')->get()->map(fn ($row) => (array) $row)->all();
        $this->info(sprintf('Réservations trouvées : %d', count($bookings)));
        $service->importBookings($bookings);

        $this->info('Import terminé.');

        return self::SUCCESS;
    }
}
