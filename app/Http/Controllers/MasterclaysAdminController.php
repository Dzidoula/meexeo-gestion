<?php
namespace App\Http\Controllers;

use Illuminate\View\View;

class MasterclaysAdminController extends Controller
{
    public function finance(): View
    {
        return view('masterclays.finance', [
            'rows' => [
                ['date' => '21/05/2025', 'label' => 'Paiement réservation', 'module' => 'Résidence', 'type' => 'Entrée', 'amount' => '+450 000 FCFA', 'up' => true],
                ['date' => '20/05/2025', 'label' => 'Achat matériel', 'module' => 'Événementiel', 'type' => 'Sortie', 'amount' => '-280 000 FCFA', 'up' => false],
                ['date' => '19/05/2025', 'label' => 'Paiement séjour', 'module' => 'Hôtel', 'type' => 'Entrée', 'amount' => '+150 000 FCFA', 'up' => true],
                ['date' => '18/05/2025', 'label' => 'Salaire équipe', 'module' => 'Ressources humaines', 'type' => 'Sortie', 'amount' => '-1 200 000 FCFA', 'up' => false],
                ['date' => '17/05/2025', 'label' => 'Loyer perçu', 'module' => 'Gestion locative', 'type' => 'Entrée', 'amount' => '+320 000 FCFA', 'up' => true],
            ],
        ]);
    }

    public function reports(): View
    {
        return view('masterclays.reports', [
            'cards' => [
                ['label' => 'Résidence', 'color' => '#2563EB'], ['label' => 'Hôtel', 'color' => '#16A34A'],
                ['label' => 'Locatif', 'color' => '#EA580C'], ['label' => 'Événementiel', 'color' => '#7C3AED'],
                ['label' => 'Véhicules', 'color' => '#0284C7'], ['label' => 'Stock', 'color' => '#0D9488'],
                ['label' => 'RH', 'color' => '#DB2777'], ['label' => 'Clients', 'color' => '#0891B2'],
                ['label' => 'Finances', 'color' => '#059669'],
            ],
        ]);
    }

    public function permissions(): View
    {
        $users = [
            ['name' => 'Administrateur', 'role' => 'Super Administrateur', 'access' => 'Tous les modules', 'status' => 'Actif'],
            ['name' => "N'Da Marie", 'role' => 'Comptabilité', 'access' => 'Comptabilité, Rapports', 'status' => 'Actif'],
            ['name' => 'Jean Kouakou', 'role' => 'Hôtel', 'access' => 'Hôtel, Réservations', 'status' => 'Actif'],
            ['name' => 'Silué Bakary', 'role' => 'Véhicules', 'access' => 'Véhicules', 'status' => 'Actif'],
            ['name' => 'Aya Koffi', 'role' => 'Stock', 'access' => 'Gestion de stock', 'status' => 'Congé'],
        ];

        return view('masterclays.permissions', [
            'users' => array_map(fn (array $u) => [...$u, 'pill_color' => \App\Support\StatusPillColor::hex($u['status'])], $users),
        ]);
    }

    public function notifications(): View
    {
        return view('masterclays.notifications', [
            'items' => [
                ['title' => 'Nouvelle réservation', 'module' => 'Résidence', 'detail' => 'Chambre 205', 'time' => 'Il y a 10 min', 'color' => '#2563EB'],
                ['title' => 'Paiement reçu', 'module' => 'Gestion locative', 'detail' => 'Contrat #GL-0258', 'time' => 'Il y a 30 min', 'color' => '#EA580C'],
                ['title' => 'Nouvel événement', 'module' => 'Événementiel', 'detail' => 'Mariage Koffi & Aya', 'time' => 'Il y a 1h', 'color' => '#7C3AED'],
                ['title' => 'Sortie véhicule', 'module' => 'Véhicules', 'detail' => 'Toyota Hilux', 'time' => 'Il y a 2h', 'color' => '#0284C7'],
                ['title' => 'Entrée en stock', 'module' => 'Gestion de stock', 'detail' => '50 unités', 'time' => 'Il y a 3h', 'color' => '#0D9488'],
                ['title' => 'Nouvel employé', 'module' => 'Ressources humaines', 'detail' => 'Jean KOUADIO', 'time' => 'Il y a 5h', 'color' => '#DB2777'],
                ['title' => 'Loyer impayé', 'module' => 'Gestion locative', 'detail' => '5 locataires', 'time' => 'Il y a 2h', 'color' => '#DC2626'],
                ['title' => 'Maintenance véhicule', 'module' => 'Véhicules', 'detail' => '3 véhicules à vérifier', 'time' => 'Il y a 5h', 'color' => '#DC2626'],
                ['title' => 'Stock faible', 'module' => 'Gestion de stock', 'detail' => '12 articles en rupture', 'time' => 'Il y a 1j', 'color' => '#DC2626'],
            ],
        ]);
    }

    public function security(): View
    {
        return view('masterclays.security', [
            'log' => [
                ['user' => 'Administrateur', 'action' => 'Connexion réussie', 'date' => '21/05/2025 08:12'],
                ['user' => "N'Da Marie", 'action' => 'Export rapport financier', 'date' => '20/05/2025 17:40'],
                ['user' => 'Jean Kouakou', 'action' => 'Modification réservation #204', 'date' => '20/05/2025 14:05'],
                ['user' => 'Administrateur', 'action' => 'Ajout utilisateur Silué Bakary', 'date' => '19/05/2025 10:22'],
            ],
        ]);
    }

    public function settings(): View
    {
        return view('masterclays.settings');
    }
}
