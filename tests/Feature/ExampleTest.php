<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Instance;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $instance = Instance::where('instance_slug', 'demo-instance')->first();

        $response = $this->get('/' . $instance->instance_slug . '/booking');

        $response->assertStatus(200);
    }
}
