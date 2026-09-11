<?php
// tests/Unit/StatusPresenterTest.php
namespace Tests\Unit;

use App\Support\StatusPresenter;
use PHPUnit\Framework\TestCase;

class StatusPresenterTest extends TestCase
{
    public function test_it_maps_a_known_status_to_a_french_label_and_classes(): void
    {
        $paid = StatusPresenter::for('occupied');

        $this->assertSame('Occupé', $paid['label']);
        $this->assertStringContainsString('bg-ok-bg', $paid['wrapper']);
        $this->assertStringContainsString('bg-ok-puce', $paid['dot']);
    }

    public function test_it_maps_every_status_used_by_this_plan(): void
    {
        foreach (['occupied', 'vacant', 'works', 'active', 'former', 'pending', 'blacklisted', 'ended'] as $status) {
            $presented = StatusPresenter::for($status);
            $this->assertNotSame('', $presented['label'], "Statut sans libellé : {$status}");
        }
    }

    public function test_it_fails_loudly_on_an_unknown_status(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StatusPresenter::for('not-a-status');
    }
}
