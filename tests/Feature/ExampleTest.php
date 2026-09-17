<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_root_serves_the_public_home_page(): void
    {
        $this->get('/')->assertOk();
    }
}
