<?php
namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticModuleController extends Controller
{
    public function show(string $module): View
    {
        $data = config("masterclays_modules.modules.{$module}");

        // Seuls les modules génériques (kpis non vides) sont servis ici ;
        // 'locative' et les 6 pages d'administration ont leur propre route.
        abort_if(! $data || empty($data['kpis']), 404);

        $rows = collect($data['rows'])->map(fn (array $row) => [
            ...$row,
            'pill_color' => \App\Support\StatusPillColor::hex($row['status']),
        ]);

        return view('modules.generic', ['module' => $data, 'rows' => $rows]);
    }
}
