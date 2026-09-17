<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ComingSoonController extends Controller
{
    /** Une seule vue, paramétrée par activité — pas de contrôleur dupliqué 3 fois. */
    private const ACTIVITIES = [
        'taxis' => [
            'title' => 'Taxis',
            'description' => "Réservez un taxi en toute simplicité, bientôt directement depuis ce site. En attendant, contactez-nous pour organiser votre trajet.",
        ],
        'sonorisation' => [
            'title' => 'Sonorisation',
            'description' => "La location de matériel de sonorisation pour vos événements arrive bientôt sur ce site. Contactez-nous pour vos besoins actuels.",
        ],
        'podiums' => [
            'title' => 'Podiums',
            'description' => "La gestion de podiums pour vos événements arrive bientôt sur ce site. Contactez-nous pour vos besoins actuels.",
        ],
    ];

    public function show(string $activity): View
    {
        abort_unless(array_key_exists($activity, self::ACTIVITIES), 404);

        return view('public.coming-soon', self::ACTIVITIES[$activity]);
    }
}
