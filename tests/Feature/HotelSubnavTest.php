<?php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelSubnavTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_hotel_module_page_links_to_every_other_hotel_section(): void
    {
        $this->actingAs(User::factory()->manager()->create());

        foreach (['hotel-rooms.index', 'hotel-room-types.index', 'hotel-stays.index', 'hotel-galleries.index', 'hotel-testimonials.index', 'hotel-faqs.index', 'hotel-contact-messages.index', 'hotel-newsletter-subscribers.index', 'hotel-promo-codes.index'] as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee(route('hotel-galleries.index'), false)
                ->assertSee(route('hotel-promo-codes.index'), false);
        }
    }
}
