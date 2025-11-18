<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MCP Server Class
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of your MCP Server class that extends
    | Laravel\Mcp\Server.
    |
    */
    'server_class' => env('MCP_DOCS_SERVER_CLASS'),

    /*
    |--------------------------------------------------------------------------
    | Server URL
    |--------------------------------------------------------------------------
    |
    | The base URL pattern for your MCP server endpoint. This will be used
    | to generate installation commands. You can use {server} placeholder
    | which will be replaced with the server name.
    |
    */
    'server_url_pattern' => env('MCP_DOCS_SERVER_URL', '{base_url}/mcp/{server}'),

    /*
    |--------------------------------------------------------------------------
    | Server Name
    |--------------------------------------------------------------------------
    |
    | The server name/identifier used in the URL. This is typically the
    | kebab-case version of your server name.
    |
    */
    'server_name' => env('MCP_DOCS_SERVER_NAME', 'server'),

    /*
    |--------------------------------------------------------------------------
    | Installation Commands
    |--------------------------------------------------------------------------
    |
    | Customize the installation commands for different platforms. The {url}
    | placeholder will be replaced with the actual server URL.
    |
    */
    'installation_commands' => [
        'cursor' => 'cursor://install-mcp?url={url}',
        'vscode' => 'vscode://install-mcp?url={url}',
        'claude code' => 'claude mcp add {url}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Message
    |--------------------------------------------------------------------------
    |
    | Customize the authentication warning message shown in the documentation.
    | Set to null to hide the authentication section.
    |
    */
    'auth_message' => 'This MCP server requires authentication. Include your Sanctum token in the Authorization header:',

    /*
    |--------------------------------------------------------------------------
    | Support Message
    |--------------------------------------------------------------------------
    |
    | Customize the support/help message shown at the bottom of the documentation.
    | Set to null to hide the support section.
    |
    */
    'support_message' => null,

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the route that will serve the documentation page.
    |
    */
    'route' => [
        'enabled' => env('MCP_DOCS_ROUTE_ENABLED', true),
        'path' => env('MCP_DOCS_ROUTE_PATH', '/docs/mcp'),
        'name' => env('MCP_DOCS_ROUTE_NAME', 'mcp.docs'),
        'middleware' => env('MCP_DOCS_ROUTE_MIDDLEWARE', 'web'),
    ],
];

