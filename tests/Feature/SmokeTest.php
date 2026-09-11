<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_boots_and_migrations_run(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('users'));
    }
}
