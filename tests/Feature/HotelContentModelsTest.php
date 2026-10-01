<?php
namespace Tests\Feature;

use App\Models\HotelGallery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelContentModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_native_gallery_image_resolves_to_the_local_disk(): void
    {
        $gallery = HotelGallery::create(['image_path' => 'hotel-galleries/photo.jpg']);
        $this->assertSame(asset('storage/hotel-galleries/photo.jpg'), $gallery->display_url);
    }

    public function test_a_touvalem_origin_gallery_image_resolves_to_the_touvalem_domain(): void
    {
        $gallery = HotelGallery::create([
            'image_path' => 'gallery/photo.jpg',
            'external_source' => 'residence_touvalem',
            'external_id' => 7,
        ]);
        $this->assertSame('https://residencetouvalem.com/storage/gallery/photo.jpg', $gallery->display_url);
    }
}
