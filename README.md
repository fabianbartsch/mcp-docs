# Laravel MCP Documentation Generator

[![Latest Version](https://img.shields.io/github/v/tag/fabianbartsch/mcp-docs?style=flat-square)](https://github.com/fabianbartsch/mcp-docs/releases)
[![Packagist](https://img.shields.io/packagist/v/fabianbartsch/mcp-docs.svg?style=flat-square)](https://packagist.org/packages/fabianbartsch/mcp-docs)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

Automatically generate beautiful documentation pages for your Laravel MCP (Model Context Protocol) servers. This package extracts metadata from your MCP server classes and generates comprehensive documentation with tools, resources, prompts, and installation instructions.

## Features

- 🚀 **Automatic Documentation**: Automatically extracts tools, resources, and prompts from your MCP server class
- 🎨 **Beautiful UI**: Modern, responsive documentation pages built with Tailwind CSS
- ⚙️ **Highly Configurable**: Customize server URLs, installation commands, and messages
- 📦 **Easy Integration**: Works out of the box with Laravel MCP servers
- 🔧 **Extensible**: Publish and customize views to match your brand

## Installation

Install the package via Composer:

```bash
composer require fabianbartsch/mcp-docs
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=mcp-docs-config
```

This will create `config/mcp-docs.php`. Configure your MCP server class:

```php
'server_class' => env('MCP_DOCS_SERVER_CLASS', App\Mcp\Servers\YourServer::class),
'server_name' => env('MCP_DOCS_SERVER_NAME', 'your-server'),
'server_url_pattern' => env('MCP_DOCS_SERVER_URL', '{base_url}/mcp/{server}'),
```

Or set it in your `.env` file:

```env
MCP_DOCS_SERVER_CLASS=App\Mcp\Servers\YourServer
MCP_DOCS_SERVER_NAME=your-server
MCP_DOCS_SERVER_URL=https://yourdomain.com/mcp/{server}
```

## Usage

### Automatic Route Registration

By default, the package registers a route at `/docs/mcp`. You can customize this in the config file or disable it:

```php
'route' => [
    'enabled' => true,
    'path' => '/docs/mcp',
    'name' => 'mcp.docs',
    'middleware' => 'web',
],
```

### Manual Route Registration

If you prefer to register the route manually, disable automatic registration and add it to your `routes/web.php`:

```php
use FabianBartsch\McpDocs\Controllers\McpDocumentationController;

Route::get('/docs/mcp', [McpDocumentationController::class, 'index'])
    ->name('mcp.docs');
```

### Customizing Views

Publish the views to customize them:

```bash
php artisan vendor:publish --tag=mcp-docs-views
```

Views will be published to `resources/views/vendor/mcp-docs/`. You can now customize:

- `index.blade.php` - Main documentation page
- `mcp-docs.blade.php` - Layout template
- `partials/` - Reusable components

### Customizing Messages

You can customize authentication and support messages in the config file:

```php
'auth_message' => 'This MCP server requires authentication. Include your Sanctum token in the Authorization header:',
'support_message' => 'For questions or issues, please contact support@example.com.',
```

Set to `null` to hide a section entirely.

### Custom Installation Commands

Customize installation commands for different platforms:

```php
'installation_commands' => [
    'cursor' => 'cursor://install-mcp?url={url}',
    'vscode' => 'vscode://install-mcp?url={url}',
    'claude code' => 'claude mcp add {url}',
    'custom' => 'your-custom-command {url}',
],
```

## Requirements

- PHP >= 8.1
- Laravel >= 10.0 (supports Laravel 10, 11, and 12)
- [laravel/mcp](https://github.com/laravel/mcp) >= 0.1.0

> **Note:** This package matches the same Laravel and PHP version requirements as `laravel/mcp` for maximum compatibility.

## MCP Server Structure

Your MCP server class should extend `Laravel\Mcp\Server` and define:

```php
<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class YourServer extends Server
{
    protected string $name = 'Your Server Name';
    protected string $version = '1.0.0';
    protected string $instructions = 'Server description';
    
    protected array $tools = [
        YourTool::class,
    ];
    
    protected array $resources = [
        YourResource::class,
    ];
    
    protected array $prompts = [
        YourPrompt::class,
    ];
}
```

## How It Works

The package uses PHP reflection to:

1. Extract server metadata (name, version, instructions)
2. Discover all registered tools, resources, and prompts
3. Extract metadata from each component (name, title, description, parameters)
4. Generate a comprehensive documentation page

## Examples

### Basic Usage

After installation and configuration, visit `/docs/mcp` to see your documentation.

### Extending the Controller

You can extend the controller to add custom logic:

```php
<?php

namespace App\Http\Controllers;

use FabianBartsch\McpDocs\Controllers\McpDocumentationController as BaseController;

class McpDocumentationController extends BaseController
{
    protected function generateServerUrl(): string
    {
        // Custom URL generation logic
        return 'https://custom-url.com/mcp/server';
    }
}
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- [Fabian Bartsch](https://github.com/fabianbartsch)

