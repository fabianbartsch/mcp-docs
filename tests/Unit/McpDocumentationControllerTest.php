<?php

declare(strict_types=1);

namespace FabianBartsch\McpDocs\Tests\Unit;

use FabianBartsch\McpDocs\Controllers\McpDocumentationController;
use FabianBartsch\McpDocs\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class McpDocumentationControllerTest extends TestCase
{
    /**
     * Test that controller returns 500 when server class is not configured.
     */
    public function test_index_returns_error_when_server_class_not_configured(): void
    {
        Config::set('mcp-docs.server_class', null);

        $response = $this->get('/docs/mcp');

        // Laravel returns a 500 error page when abort() is called
        $response->assertStatus(500);
    }

    /**
     * Test that controller returns 500 when server class does not exist.
     */
    public function test_index_returns_error_when_server_class_not_found(): void
    {
        Config::set('mcp-docs.server_class', 'NonExistent\\Class');

        $response = $this->get('/docs/mcp');

        // Laravel returns a 500 error page when abort() is called
        $response->assertStatus(500);
    }

    /**
     * Test that controller can be instantiated.
     */
    public function test_controller_can_be_instantiated(): void
    {
        $controller = $this->app->make(McpDocumentationController::class);

        $this->assertInstanceOf(McpDocumentationController::class, $controller);
    }
}

