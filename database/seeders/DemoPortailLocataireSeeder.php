<?php

namespace Database\Seeders;

use App\Enums\LeaseStatus;
use App\Enums\MaritalStatus;
use App\Enums\PaymentMethod;
use App\Enums\PropertyStatus;
use App\Enums\PortalDocumentCategory;
use App\Enums\PropertyType;
use App\Enums\TenantStatus;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\PortalDocument;
use App\Models\Property;
use App\Models\RepairRequest;
use App\Models\Tenant;
use App\Models\TenantMessage;
use Illuminate\Database\Seeder;

/**
 * Demo data for the tenant portal. Each tenant is built around a situation worth
 * showing: up to date, late, partially paid, a pending proof, open repairs.
 * Phone numbers are the login identifiers — they are printed at the end.
 */
class DemoPortailLocataireSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = [
            [
                'tenant' => [
                    'last_name' => 'Kouassi', 'first_names' => 'Affoué Marie-Claire',
                    'phone1' => '0707010203', 'phone2' => '0101020304',
                    'email' => 'mc.kouassi@example.ci',
                    'occupation' => 'Comptable', 'workplace' => 'Cabinet Diarra & Associés',
                    'marital_status' => MaritalStatus::Married, 'id_number' => 'CI00234518',
                    'emergency_name' => 'Kouassi Bernard', 'emergency_phone' => '0505060708',
                ],
                'property' => [
                    'title' => 'Appartement 3 pièces — Les Cocotiers',
                    'type' => PropertyType::FlatThreeBedrooms,
                    'city' => 'Abidjan', 'commune' => 'Cocody', 'district' => 'Riviera 3',
                    'rooms' => 3, 'area_sqm' => 95, 'monthly_rent' => 250000, 'deposit' => 500000,
                ],
                'lease'    => ['monthly_rent' => 250000, 'deposit_paid' => 500000, 'due_day' => 5, 'months_ago' => 8],
                'behaviour' => 'up_to_date',
            ],
            [
                'tenant' => [
                    'last_name' => 'Traoré', 'first_names' => 'Ibrahim',
                    'phone1' => '0708020304', 'phone2' => null,
                    'email' => 'i.traore@example.ci',
                    'occupation' => 'Chauffeur poids lourd', 'workplace' => 'Transports Sahel CI',
                    'marital_status' => MaritalStatus::Single, 'id_number' => 'CI00871204',
                    'emergency_name' => 'Traoré Aminata', 'emergency_phone' => '0709101112',
                ],
                'property' => [
                    'title' => 'Studio meublé — Marcory Résidentiel',
                    'type' => PropertyType::Studio,
                    'city' => 'Abidjan', 'commune' => 'Marcory', 'district' => 'Zone 4',
                    'rooms' => 1, 'area_sqm' => 32, 'monthly_rent' => 90000, 'deposit' => 180000,
                ],
                'lease'    => ['monthly_rent' => 90000, 'deposit_paid' => 180000, 'due_day' => 10, 'months_ago' => 5],
                'behaviour' => 'late',
            ],
            [
                'tenant' => [
                    'last_name' => 'N\'Guessan', 'first_names' => 'Yao Prosper',
                    'phone1' => '0709030405', 'phone2' => '2722445566',
                    'email' => 'p.nguessan@example.ci',
                    'occupation' => 'Enseignant', 'workplace' => 'Lycée Moderne de Yopougon',
                    'marital_status' => MaritalStatus::Married, 'id_number' => 'CI00445902',
                    'emergency_name' => 'N\'Guessan Adjoua', 'emergency_phone' => '0712131415',
                ],
                'property' => [
                    'title' => 'Villa basse 4 pièces — Yopougon Selmer',
                    'type' => PropertyType::Villa,
                    'city' => 'Abidjan', 'commune' => 'Yopougon', 'district' => 'Selmer',
                    'rooms' => 4, 'area_sqm' => 140, 'monthly_rent' => 180000, 'deposit' => 360000,
                ],
                'lease'    => ['monthly_rent' => 180000, 'deposit_paid' => 360000, 'due_day' => 1, 'months_ago' => 14],
                'behaviour' => 'partial',
            ],
            [
                'tenant' => [
                    'last_name' => 'Bamba', 'first_names' => 'Fatoumata',
                    'phone1' => '0501040506', 'phone2' => null,
                    'email' => 'f.bamba@example.ci',
                    'occupation' => 'Commerçante', 'workplace' => 'Marché de Treichville',
                    'marital_status' => MaritalStatus::Widowed, 'id_number' => 'CI00692371',
                    'emergency_name' => 'Bamba Sékou', 'emergency_phone' => '0515161718',
                ],
                'property' => [
                    'title' => 'Magasin — Treichville Avenue 12',
                    'type' => PropertyType::Shop,
                    'city' => 'Abidjan', 'commune' => 'Treichville', 'district' => 'Avenue 12',
                    'rooms' => 2, 'area_sqm' => 48, 'monthly_rent' => 150000, 'deposit' => 300000,
                ],
                'lease'    => ['monthly_rent' => 150000, 'deposit_paid' => 300000, 'due_day' => 5, 'months_ago' => 3],
                'behaviour' => 'pending_proof',
            ],
        ];

        // Re-runnable: a partially failed run would otherwise leave a duplicate
        // tenant on the same phone number, and login resolves the oldest one.
        // Tenant::phone1 is canonicalized on write (+225XXXXXXXXXX), so the
        // purge must look up the same canonical form, not the raw scenario value.
        $this->purgePreviousRun(
            array_map(fn ($s) => \App\Support\PhoneNumber::ivoirianE164($s['tenant']['phone1']), $scenarios),
            array_map(fn ($s) => $s['property']['title'], $scenarios),
        );

        $credentials = [];

        foreach ($scenarios as $s) {
            $tenant = Tenant::create($s['tenant'] + ['status' => TenantStatus::Active]);

            $property = Property::create($s['property'] + [
                'status' => PropertyStatus::Occupied,
            ]);

            $start = now()->subMonths($s['lease']['months_ago'])->startOfMonth();

            $lease = Lease::create([
                'property_id'  => $property->id,
                'tenant_id'    => $tenant->id,
                'start_date'   => $start->toDateString(),
                'monthly_rent' => $s['lease']['monthly_rent'],
                'deposit_paid' => $s['lease']['deposit_paid'],
                'due_day'      => $s['lease']['due_day'],
                'status'       => LeaseStatus::Active,
            ]);

            $this->seedPayments($lease, $start, $s['behaviour']);
            $this->seedRepairs($tenant, $lease, $s['behaviour']);
            $this->seedDocuments($tenant, $lease, $start);
            $this->seedMessages($tenant, $lease, $s['behaviour']);

            $credentials[] = [
                'name'      => $tenant->first_names.' '.$tenant->last_name,
                // Stocké canonique (+225...), mais la connexion ne demande que
                // la partie locale depuis la correction du format de saisie.
                'phone'     => preg_replace('/^\+225/', '', $tenant->phone1),
                'situation' => $s['behaviour'],
                'property'  => $property->title,
            ];
        }

        $this->report($credentials);
    }

    /**
     * @param  array<int, string>  $phones
     * @param  array<int, string>  $propertyTitles
     */
    private function purgePreviousRun(array $phones, array $propertyTitles): void
    {
        // Leases, payments and repair requests all cascade from the tenant.
        $removed = Tenant::whereIn('phone1', $phones)->get()->each->delete()->count();

        Property::whereIn('title', $propertyTitles)->delete();

        if ($removed > 0) {
            $this->command->warn("Données de démo précédentes supprimées ({$removed} locataire(s)).");
        }
    }

    private function seedPayments(Lease $lease, \Illuminate\Support\Carbon $start, string $behaviour): void
    {
        $cursor  = $start->copy();
        $current = now()->startOfMonth();
        $methods = [PaymentMethod::Wave, PaymentMethod::OrangeMoney, PaymentMethod::Cash, PaymentMethod::BankTransfer];

        while ($cursor->lte($current)) {
            $isCurrentMonth = $cursor->equalTo($current);
            $isLastMonth    = $cursor->equalTo($current->copy()->subMonth());

            // Every month before the current one is settled, except where the
            // scenario calls for a gap.
            $skip = match ($behaviour) {
                'late'          => $isCurrentMonth || $isLastMonth,
                'partial'       => $isCurrentMonth,
                'pending_proof' => $isCurrentMonth,
                default         => $isCurrentMonth,
            };

            if (! $skip) {
                $amount = ($behaviour === 'partial' && $isLastMonth)
                    ? (int) ($lease->monthly_rent * 0.6)
                    : $lease->monthly_rent;

                $method = $methods[$cursor->month % count($methods)];

                Payment::create([
                    'lease_id'   => $lease->id,
                    'month'      => $cursor->toDateString(),
                    'paid_on'    => $cursor->copy()->setDay(min($lease->due_day + 1, $cursor->daysInMonth))->toDateString(),
                    'amount'     => $amount,
                    'method'     => $method,
                    'reference'  => $method === PaymentMethod::Cash ? null : strtoupper(substr(md5($lease->id.$cursor->format('Ym')), 0, 10)),
                    'proof_path' => 'payments/proofs/recu-'.$lease->id.'-'.$cursor->format('Y-m').'.jpg',
                ]);
            }

            $cursor->addMonth();
        }

        if ($behaviour === 'up_to_date') {
            Payment::create([
                'lease_id'   => $lease->id,
                'month'      => $current->toDateString(),
                'paid_on'    => $current->copy()->setDay(min($lease->due_day, $current->daysInMonth))->toDateString(),
                'amount'     => $lease->monthly_rent,
                'method'     => PaymentMethod::Wave,
                'reference'  => strtoupper(substr(md5($lease->id.'current'), 0, 10)),
                'proof_path' => 'payments/proofs/recu-'.$lease->id.'-'.$current->format('Y-m').'.jpg',
            ]);
        }

        // An unverified upload awaiting the manager's review.
        if ($behaviour === 'pending_proof') {
            Payment::create([
                'lease_id'      => $lease->id,
                'month'         => $current->toDateString(),
                'paid_on'       => now()->toDateString(),
                'amount'        => $lease->monthly_rent,
                'method'        => PaymentMethod::Cash,
                'proof_path'    => 'payments/proofs/demo-recu-wave.jpg',
                'portal_status' => 'pending',
                'reference'     => 'PREUVE-DEMO01',
                'notes'         => 'Preuve envoyée via portail — en attente de vérification',
            ]);
        }
    }

    private function seedRepairs(Tenant $tenant, Lease $lease, string $behaviour): void
    {
        $sets = [
            'up_to_date' => [
                ['plomberie', 'Fuite au niveau du siphon sous l\'évier de la cuisine. L\'eau s\'écoule dès qu\'on ouvre le robinet.', 'moyenne', 'repare', 'Siphon remplacé le 12. Plombier passé.'],
                ['climatisation', 'Le split de la chambre principale ne refroidit plus, il souffle de l\'air tiède.', 'faible', 'en_cours', 'Technicien attendu cette semaine pour recharge de gaz.'],
            ],
            'late' => [
                ['serrure', 'La serrure de la porte d\'entrée force beaucoup, la clé tourne mal depuis deux jours.', 'urgente', 'recu', null],
            ],
            'partial' => [
                ['electricite', 'Deux prises du salon ne fonctionnent plus du tout depuis l\'orage de la semaine dernière.', 'urgente', 'en_cours', 'Électricien a diagnostiqué un disjoncteur à remplacer.'],
                ['peinture', 'Traces d\'humidité et peinture qui cloque sur le mur nord de la chambre.', 'faible', 'cloture', 'Traité et repeint. Validé par le locataire.'],
            ],
            'pending_proof' => [
                ['autre', 'Le rideau métallique du magasin se bloque à mi-hauteur, difficile à fermer le soir.', 'urgente', 'recu', null],
            ],
        ];

        foreach ($sets[$behaviour] ?? [] as $i => [$type, $description, $urgency, $status, $notes]) {
            RepairRequest::create([
                'tenant_id'   => $tenant->id,
                'lease_id'    => $lease->id,
                'type'        => $type,
                'description' => $description,
                'urgency'     => $urgency,
                'status'      => $status,
                'notes'       => $notes,
            ])->forceFill([
                'created_at' => now()->subDays(($i + 1) * 9),
            ])->save();
        }
    }

    private function seedDocuments(Tenant $tenant, Lease $lease, \Illuminate\Support\Carbon $start): void
    {
        // Le bail signé.
        PortalDocument::create([
            'tenant_id'     => $tenant->id,
            'lease_id'      => $lease->id,
            'category'      => PortalDocumentCategory::Lease,
            'path'          => 'portal-documents/demo/contrat-location.pdf',
            'original_name' => 'Contrat de location '.$start->year.'.pdf',
            'size'          => 239_904,
            'issued_at'     => $start->copy(),
        ]);

        // Une quittance par mois réglé, du plus récent au plus ancien.
        foreach ($lease->payments()->whereNull('portal_status')->latest('month')->take(3)->get() as $p) {
            PortalDocument::create([
                'tenant_id'     => $tenant->id,
                'lease_id'      => $lease->id,
                'category'      => PortalDocumentCategory::Receipt,
                'path'          => 'portal-documents/demo/quittance-'.$p->month->format('Y-m').'.pdf',
                'original_name' => 'Quittance — '.ucfirst($p->month->isoFormat('MMMM YYYY')).'.pdf',
                'size'          => random_int(80_000, 95_000),
                'period'        => $p->month,
                'issued_at'     => $p->paid_on,
            ]);
        }

        PortalDocument::create([
            'tenant_id'     => $tenant->id,
            'lease_id'      => $lease->id,
            'category'      => PortalDocumentCategory::Other,
            'path'          => 'portal-documents/demo/etat-des-lieux.pdf',
            'original_name' => "État des lieux d'entrée.pdf",
            'size'          => 337_612,
            'issued_at'     => $start->copy(),
        ]);
    }

    private function seedMessages(Tenant $tenant, Lease $lease, string $behaviour): void
    {
        $welcome = TenantMessage::create([
            'tenant_id' => $tenant->id,
            'sender'    => 'manager',
            'subject'   => 'Bienvenue sur votre portail locataire MEEXEO',
            'body'      => "Bonjour {$tenant->first_names},\n\n"
                ."Nous sommes ravis de vous accueillir sur votre espace locataire. Vous y "
                ."retrouvez vos loyers mois par mois, vos quittances, et vous pouvez y signaler "
                ."tout problème dans votre logement.\n\n"
                ."Après chaque paiement par Wave ou Orange Money, pensez à envoyer la capture "
                ."de votre reçu depuis l'onglet « Loyers & paiements ».\n\n"
                ."Cordialement,\nMEEXEO IMMOBILIER",
            'read_at'   => null,
        ]);

        $welcome->forceFill(['created_at' => $lease->start_date])->save();

        if (in_array($behaviour, ['late', 'partial'], true)) {
            TenantMessage::create([
                'tenant_id' => $tenant->id,
                'sender'    => 'manager',
                'subject'   => 'Rappel : loyer du mois en cours',
                'body'      => "Bonjour,\n\n"
                    ."Nous vous rappelons que le loyer est dû le {$lease->due_day} de chaque mois. "
                    ."Votre règlement n'est pas encore parvenu.\n\n"
                    ."Si vous avez déjà payé, envoyez-nous simplement la preuve depuis le portail "
                    ."et nous régulariserons votre situation.\n\n"
                    ."Cordialement,\nMEEXEO IMMOBILIER",
                'read_at'   => null,
            ])->forceFill(['created_at' => now()->subDays(2)])->save();
        }
    }

    /** @param array<int, array<string, string>> $credentials */
    private function report(array $credentials): void
    {
        $labels = [
            'up_to_date'    => 'à jour',
            'late'          => '2 mois de retard',
            'partial'       => 'paiement partiel',
            'pending_proof' => 'preuve en attente de validation',
        ];

        $this->command->newLine();
        $this->command->info('Comptes locataires de démonstration — se connecter avec le numéro de téléphone :');
        $this->command->newLine();

        $this->command->table(
            ['Nom', 'Téléphone (identifiant)', 'Situation', 'Bien'],
            array_map(fn ($c) => [
                $c['name'],
                $c['phone'],
                $labels[$c['situation']] ?? $c['situation'],
                $c['property'],
            ], $credentials)
        );

        $this->command->newLine();
        $this->command->warn('Le code à 6 chiffres s\'affiche directement sur la page de vérification.');
    }
}
