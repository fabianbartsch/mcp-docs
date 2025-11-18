<?php

declare(strict_types=1);

namespace FabianBartsch\McpDocs\Providers;

use FabianBartsch\McpDocs\Controllers\McpDocumentationController;
use FabianBartsch\McpDocs\Services\McpDocumentationService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class McpDocsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/mcp-docs.php',
            'mcp-docs'
        );

        $this->app->singleton(McpDocumentationService::class, function ($app) {
            return new McpDocumentationService($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../Config/mcp-docs.php' => config_path('mcp-docs.php'),
        ], 'mcp-docs-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/mcp-docs'),
        ], 'mcp-docs-views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mcp-docs');

        if (config('mcp-docs.route.enabled', true)) {
            $this->registerRoutes();
        }
    }

    /**
     * Register the documentation route.
     */
    protected function registerRoutes(): void
    {
        Route::middleware(config('mcp-docs.route.middleware', 'web'))
            ->get(
                config('mcp-docs.route.path', '/docs/mcp'),
                [McpDocumentationController::class, 'index']
            )
            ->name(config('mcp-docs.route.name', 'mcp.docs'));
    }
}

