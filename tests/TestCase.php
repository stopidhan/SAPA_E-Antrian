<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

        $instance = \App\Models\Instance::firstOrCreate(
            ['instance_slug' => 'demo-instance'],
            ['instance_name' => 'Demo Instance', 'instance_code' => 'demo']
        );

        $tenantManager = $this->app->make(\App\Services\TenantManager::class);
        $tenantManager->setInstance($instance);

        \Illuminate\Support\Facades\URL::defaults(['instance_slug' => 'demo-instance']);
    }
}
