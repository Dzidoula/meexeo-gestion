<?php
namespace Tests\Feature;

use App\Models\Lease;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFormLeasePickerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Régression : le sélecteur de bail utilisait un `<form method="GET">`
     * imbriqué dans le `<form method="POST">` d'enregistrement, ce que le
     * HTML interdit. Le navigateur ignorait le formulaire interne et
     * `onchange="this.form.submit()"` soumettait donc le formulaire POST
     * (payments.store) au lieu de recharger la page en GET
     * (payments.create?lease=...). Ce test prouve que le contrat GET avec
     * paramètre de requête fonctionne bien côté serveur.
     */
    public function test_the_picker_lists_all_active_leases_without_selecting_one(): void
    {
        $first = Lease::factory()->create();
        $second = Lease::factory()->create();

        $response = $this->actingAs(User::factory()->manager()->create())
            ->get('/paiements/nouveau')
            ->assertOk();

        $response->assertSee($first->tenant->full_name)
            ->assertSee($second->tenant->full_name)
            ->assertDontSee('2. Mois concerné')
            ->assertDontSee('3. Montant et date');

        // Régression : le sélecteur doit naviguer directement en GET, pas
        // soumettre le `<form method="POST">` englobant (bug du formulaire
        // imbriqué que le HTML interdit).
        $response->assertSee(
            'onchange="window.location.href = \''.route('payments.create').'\'',
            false
        );
    }

    public function test_selecting_a_lease_via_query_param_scopes_the_form_to_it(): void
    {
        $first = Lease::factory()->create();
        $second = Lease::factory()->create();

        $response = $this->actingAs(User::factory()->manager()->create())
            ->get('/paiements/nouveau?lease='.$second->id)
            ->assertOk();

        $response->assertSee($second->tenant->full_name)
            ->assertSee($second->property->title)
            ->assertSee('2. Mois concerné')
            ->assertSee('3. Montant et date')
            ->assertSee('value="'.$second->id.'" selected', false);
    }
}
