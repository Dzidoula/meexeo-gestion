<?php
// tests/Feature/ComponentRenderTest.php
namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ComponentRenderTest extends TestCase
{
    public function test_the_status_badge_shows_both_a_dot_and_the_word(): void
    {
        $html = Blade::render('<x-status-badge status="vacant" />');

        $this->assertStringContainsString('Libre', $html);
        $this->assertStringContainsString('bg-neutre-puce', $html);
    }

    public function test_the_stat_card_shows_its_label_and_value(): void
    {
        $html = Blade::render('<x-stat-card label="Biens gérés" value="128" />');

        $this->assertStringContainsString('Biens gérés', $html);
        $this->assertStringContainsString('128', $html);
    }

    public function test_the_page_header_shows_its_title(): void
    {
        $html = Blade::render('<x-page-header title="Biens" />');

        $this->assertStringContainsString('Biens', $html);
    }
}
