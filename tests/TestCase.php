<?php

declare(strict_types=1);

namespace FabianBartsch\McpDocs\Tests;

use FabianBartsch\McpDocs\Providers\McpDocsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('vendor:publish', ['--tag' => 'mcp-docs-config'])->assertSuccessful();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            McpDocsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Setup default environment variables for testing
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('mcp-docs.server_class', null);
        $app['config']->set('mcp-docs.server_name', 'test-server');
        $app['config']->set('mcp-docs.server_url_pattern', '{base_url}/mcp/{server}');
    }
}

