<?php
namespace Tests\Feature;

use Tests\TestCase;

class PublicComingSoonTest extends TestCase
{
    public function test_the_taxis_page_is_public_and_shows_its_own_title(): void
    {
        $this->get('/taxis')
            ->assertOk()
            ->assertSee('Taxis')
            ->assertSee('Bientôt disponible');
    }

    public function test_the_sonorisation_page_is_public_and_shows_its_own_title(): void
    {
        $this->get('/sonorisation')
            ->assertOk()
            ->assertSee('Sonorisation')
            ->assertSee('Bientôt disponible');
    }

    public function test_the_podiums_page_is_public_and_shows_its_own_title(): void
    {
        $this->get('/podiums')
            ->assertOk()
            ->assertSee('Podiums')
            ->assertSee('Bientôt disponible');
    }

    public function test_the_three_pages_have_distinct_descriptions(): void
    {
        $taxis = $this->get('/taxis')->getContent();
        $sonorisation = $this->get('/sonorisation')->getContent();
        $podiums = $this->get('/podiums')->getContent();

        $this->assertNotSame($taxis, $sonorisation);
        $this->assertNotSame($sonorisation, $podiums);
    }
}
