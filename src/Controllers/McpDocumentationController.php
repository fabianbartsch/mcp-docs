<?php

declare(strict_types=1);

namespace FabianBartsch\McpDocs\Controllers;

use FabianBartsch\McpDocs\Services\McpDocumentationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class McpDocumentationController extends Controller
{
    public function __construct(
        protected McpDocumentationService $documentationService
    ) {}

    /**
     * Display the MCP server documentation.
     */
    public function index(Request $request): View
    {
        $serverClass = Config::get('mcp-docs.server_class');

        if (! $serverClass) {
            abort(500, 'MCP server class not configured. Please set MCP_DOCS_SERVER_CLASS in your .env file.');
        }

        if (! is_string($serverClass) || ! class_exists($serverClass)) {
            abort(500, "MCP server class '{$serverClass}' not found.");
        }

        // Validate that the class extends Laravel\Mcp\Server
        if (! is_subclass_of($serverClass, \Laravel\Mcp\Server::class)) {
            abort(500, "MCP server class '{$serverClass}' must extend Laravel\\Mcp\\Server.");
        }

        // Validate that the class is instantiable
        $reflection = new \ReflectionClass($serverClass);
        if (! $reflection->isInstantiable()) {
            abort(500, "MCP server class '{$serverClass}' is not instantiable.");
        }

        $tools = $this->documentationService->getTools($serverClass);
        $resources = $this->documentationService->getResources($serverClass);
        $prompts = $this->documentationService->getPrompts($serverClass);
        $serverInfo = $this->documentationService->getServerInfo($serverClass);

        $serverUrl = $this->generateServerUrl();
        $installationCommands = $this->generateInstallationCommands($serverUrl);

        return view('mcp-docs::index', compact(
            'tools',
            'resources',
            'prompts',
            'serverInfo',
            'installationCommands',
            'serverUrl'
        ));
    }

    /**
     * Generate the server URL based on configuration.
     */
    protected function generateServerUrl(): string
    {
        $pattern = Config::get('mcp-docs.server_url_pattern', '{base_url}/mcp/{server}');
        $serverName = Config::get('mcp-docs.server_name', 'server');
        $baseUrl = Config::get('app.url');

        return str_replace(
            ['{base_url}', '{server}'],
            [$baseUrl, $serverName],
            $pattern
        );
    }

    /**
     * Generate installation commands with the server URL.
     *
     * @return array<string, string>
     */
    protected function generateInstallationCommands(string $serverUrl): array
    {
        $commands = Config::get('mcp-docs.installation_commands', []);
        $encodedUrl = urlencode($serverUrl);

        return array_map(
            fn (string $command) => str_replace('{url}', $encodedUrl, $command),
            $commands
        );
    }
}

