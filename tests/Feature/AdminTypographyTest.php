<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * La page Hôtel › Chambres sert de référence typographique : police de
 * l'interface héritée du layout (Manrope) et titre de page en Newsreader
 * (font-titre). Les autres pages d'administration forçaient Helvetica et un
 * titre en gras sans empattement, ce qui les faisait paraître d'une autre
 * application.
 */
class AdminTypographyTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return [
            'Comptabilité'      => ['/comptabilite'],
            'Rapports'          => ['/rapports'],
            'Utilisateurs'      => ['/permissions'],
            'Notifications'     => ['/notifications'],
            'Sécurité'          => ['/securite'],
            'Paramètres'        => ['/parametres'],
            'Fournisseurs'      => ['/modules/fournisseurs'],
            'Clients'           => ['/modules/clients'],
            'Ressources humaines' => ['/modules/rh'],
            'Stock'             => ['/modules/stock'],
        ];
    }

    #[DataProvider('pages')]
    public function test_the_page_does_not_override_the_interface_font(string $url): void
    {
        $user = User::factory()->create(['role' => Role::Admin]);

        $html = $this->actingAs($user, 'web')->get($url)->assertOk()->getContent();

        $this->assertStringNotContainsString(
            'apple-system',
            $html,
            "$url force encore une police système au lieu d'hériter de celle du layout."
        );
    }

    #[DataProvider('pages')]
    public function test_the_page_title_uses_the_serif_title_face(string $url): void
    {
        $user = User::factory()->create(['role' => Role::Admin]);

        $html = $this->actingAs($user, 'web')->get($url)->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/<h1[^>]*class="[^"]*font-titre[^"]*"/',
            $html,
            "$url n'a pas de titre <h1> en font-titre comme la page Chambres."
        );
    }
}
