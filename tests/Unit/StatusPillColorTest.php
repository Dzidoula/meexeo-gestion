<?php
namespace Tests\Unit;

use App\Support\StatusPillColor;
use PHPUnit\Framework\TestCase;

class StatusPillColorTest extends TestCase
{
    public function test_known_statuses_map_to_the_mockups_exact_colors(): void
    {
        $this->assertSame('#16A34A', StatusPillColor::hex('Payé'));
        $this->assertSame('#16A34A', StatusPillColor::hex('Confirmée'));
        $this->assertSame('#D97706', StatusPillColor::hex('En attente'));
        $this->assertSame('#2563EB', StatusPillColor::hex('Nouvelle'));
        $this->assertSame('#DC2626', StatusPillColor::hex('Impayé'));
        $this->assertSame('#DC2626', StatusPillColor::hex('En retard'));
        $this->assertSame('#64748B', StatusPillColor::hex('Terminée'));
    }

    public function test_an_unknown_status_falls_back_to_slate(): void
    {
        $this->assertSame('#64748B', StatusPillColor::hex('Statut inconnu'));
    }
}
