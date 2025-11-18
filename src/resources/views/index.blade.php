@extends('mcp-docs::mcp-docs')

@section('title', 'MCP Documentation')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold mb-4">{{ $serverInfo['name'] }}</h1>
    <div class="text-gray-700 leading-relaxed">
        @php
            $instructions = trim($serverInfo['instructions']);
            // Split by double newlines to get paragraphs
            $paragraphs = preg_split('/\n\s*\n/', $instructions);
            foreach ($paragraphs as $para) {
                $para = trim($para);
                if (empty($para)) continue;
                
                // Check if it's a list (starts with -)
                if (preg_match('/^-\s+/m', $para)) {
                    echo '<ul class="list-disc list-inside space-y-1 my-3 ml-4">';
                    $lines = explode("\n", $para);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (preg_match('/^-\s+(.+)$/', $line, $matches)) {
                            echo '<li>' . htmlspecialchars($matches[1]) . '</li>';
                        }
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="my-3">' . nl2br(htmlspecialchars($para)) . '</p>';
                }
            }
        @endphp
    </div>
</div>

<!-- Info section -->
@include('mcp-docs::partials.info-card', [
    'title' => 'Server Information',
    'sections' => [
        [
            'title' => 'Server Details',
            'items' => [
                '<strong>Name:</strong> ' . $serverInfo['name'],
                '<strong>Version:</strong> ' . $serverInfo['version'],
                '<strong>Protocol:</strong> MCP (Model Context Protocol)',
                '<strong>Authentication:</strong> Required',
            ],
        ],
        [
            'title' => 'Capabilities',
            'items' => array_filter([
                '✓ ' . count($tools) . ' Tool' . (count($tools) !== 1 ? 's' : '') . ' Available',
                count($resources) > 0 ? '✓ ' . count($resources) . ' Resource' . (count($resources) !== 1 ? 's' : '') . ' Available' : null,
                count($prompts) > 0 ? '✓ ' . count($prompts) . ' Prompt' . (count($prompts) !== 1 ? 's' : '') . ' Available' : null,
            ]),
        ],
    ],
])

@if(config('mcp-docs.auth_message'))
<!-- Authentication section -->
@include('mcp-docs::partials.auth-warning', [
    'message' => config('mcp-docs.auth_message'),
])
@endif

<!-- Main Content: Tools -->
@php
    $toolsContent = '';
    ob_start();
@endphp
    @foreach($tools as $tool)
    <div class="border rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h3 class="font-medium text-gray-900">{{ $tool['name'] }}</h3>
                @if($tool['title'] !== $tool['name'])
                <p class="text-sm text-gray-500">{{ $tool['title'] }}</p>
                @endif
            </div>
            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                Tool
            </span>
        </div>
        <p class="text-gray-600 text-sm mb-3">{{ $tool['description'] }}</p>
        
        @if(isset($tool['parameters']['properties']) && count($tool['parameters']['properties']) > 0)
        <div class="mt-3 pt-3 border-t border-gray-200">
            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Parameters</h4>
            <div class="space-y-2">
                @foreach($tool['parameters']['properties'] as $paramName => $paramInfo)
                <div class="flex items-start gap-2">
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono text-gray-800">{{ $paramName }}</code>
                    <span class="text-xs text-gray-500">
                        @if(isset($paramInfo['required']) || in_array($paramName, $tool['parameters']['required'] ?? []))
                            <span class="text-red-600 font-semibold">required</span>
                        @else
                            <span class="text-gray-400">optional</span>
                        @endif
                        @if(isset($paramInfo['type']))
                            · {{ is_array($paramInfo['type']) ? implode(' | ', $paramInfo['type']) : $paramInfo['type'] }}
                        @endif
                        @if(isset($paramInfo['enum']))
                            · one of: {{ implode(', ', $paramInfo['enum']) }}
                        @endif
                    </span>
                </div>
                @if(isset($paramInfo['description']))
                <p class="text-xs text-gray-600 ml-0">{{ $paramInfo['description'] }}</p>
                @endif
                @endforeach
            </div>
        </div>
        @else
        <div class="mt-3 pt-3 border-t border-gray-200">
            <p class="text-xs text-gray-500">No parameters required</p>
        </div>
        @endif
    </div>
    @endforeach
@php
    $toolsContent = ob_get_clean();
@endphp
@include('mcp-docs::partials.section-card', [
    'title' => 'MCP Tools',
    'containerClass' => 'space-y-4',
    'content' => $toolsContent,
])

@if(count($resources) > 0)
<!-- Main Content: Resources -->
@php
    $resourcesContent = '';
    ob_start();
@endphp
    @foreach($resources as $resource)
    <div class="border rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h3 class="font-medium text-gray-900">{{ $resource['name'] }}</h3>
                @if($resource['title'] !== $resource['name'])
                <p class="text-sm text-gray-500">{{ $resource['title'] }}</p>
                @endif
            </div>
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                Resource
            </span>
        </div>
        <p class="text-gray-600 text-sm mb-3">{{ $resource['description'] }}</p>
        @if($resource['uri'] || $resource['mimeType'])
        <div class="mt-3 pt-3 border-t border-gray-200">
            <div class="space-y-1 text-xs">
                @if($resource['uri'])
                <div><strong>URI:</strong> <code class="bg-gray-100 px-1 rounded">{{ $resource['uri'] }}</code></div>
                @endif
                @if($resource['mimeType'])
                <div><strong>MIME Type:</strong> <code class="bg-gray-100 px-1 rounded">{{ $resource['mimeType'] }}</code></div>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endforeach
@php
    $resourcesContent = ob_get_clean();
@endphp
@include('mcp-docs::partials.section-card', [
    'title' => 'MCP Resources',
    'containerClass' => 'space-y-4',
    'content' => $resourcesContent,
])
@endif

@if(count($prompts) > 0)
<!-- Main Content: Prompts -->
@php
    $promptsContent = '';
    ob_start();
@endphp
    @foreach($prompts as $prompt)
    <div class="border rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h3 class="font-medium text-gray-900">{{ $prompt['name'] }}</h3>
                @if($prompt['title'] !== $prompt['name'])
                <p class="text-sm text-gray-500">{{ $prompt['title'] }}</p>
                @endif
            </div>
            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                Prompt
            </span>
        </div>
        <p class="text-gray-600 text-sm mb-3">{{ $prompt['description'] }}</p>
        @if(isset($prompt['arguments']['properties']) && count($prompt['arguments']['properties']) > 0)
        <div class="mt-3 pt-3 border-t border-gray-200">
            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Arguments</h4>
            <div class="space-y-2">
                @foreach($prompt['arguments']['properties'] as $argName => $argInfo)
                <div class="flex items-start gap-2">
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono text-gray-800">{{ $argName }}</code>
                    <span class="text-xs text-gray-500">
                        @if(isset($argInfo['required']) || in_array($argName, $prompt['arguments']['required'] ?? []))
                            <span class="text-red-600 font-semibold">required</span>
                        @else
                            <span class="text-gray-400">optional</span>
                        @endif
                        @if(isset($argInfo['type']))
                            · {{ is_array($argInfo['type']) ? implode(' | ', $argInfo['type']) : $argInfo['type'] }}
                        @endif
                        @if(isset($argInfo['enum']))
                            · one of: {{ implode(', ', $argInfo['enum']) }}
                        @endif
                    </span>
                </div>
                @if(isset($argInfo['description']))
                <p class="text-xs text-gray-600 ml-0">{{ $argInfo['description'] }}</p>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endforeach
@php
    $promptsContent = ob_get_clean();
@endphp
@include('mcp-docs::partials.section-card', [
    'title' => 'MCP Prompts',
    'description' => 'Pre-built conversation starters that guide the AI to help you with common tasks:',
    'containerClass' => 'space-y-4',
    'content' => $promptsContent,
])
@endif

<!-- Installation section -->
@php
    $installationContent = '';
    ob_start();
@endphp
    <!-- Server URL -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">MCP Server URL</label>
        <div class="flex items-center space-x-2">
            <input type="text" 
                   value="{{ $serverUrl }}" 
                   readonly 
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm font-mono">
            <button type="button" 
                    data-copy-text="{{ json_encode($serverUrl) }}"
                    class="copy-button bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                Copy URL
            </button>
        </div>
    </div>

    <!-- Installation instructions -->
    <div class="space-y-4">
        <h3 class="text-lg font-medium text-gray-900">Installation Instructions</h3>
        
        @foreach($installationCommands as $platform => $command)
        @php
            // Display readable version (decode URL-encoded parts)
            $readableCommand = preg_replace_callback(
                '/url=([^&"\']+)/',
                fn($matches) => 'url=' . urldecode($matches[1]),
                $command
            );
            // Also handle the claude code format
            $readableCommand = preg_replace_callback(
                '/claude mcp add ([^\s"\']+)/',
                fn($matches) => 'claude mcp add ' . urldecode($matches[1]),
                $readableCommand
            );
        @endphp
        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-gray-900 capitalize">{{ $platform }}</h4>
                <button type="button" 
                        data-copy-text="{{ json_encode($command) }}"
                        class="copy-button text-blue-600 hover:text-blue-800 text-sm">
                    Copy Command
                </button>
            </div>
            <code class="text-sm bg-gray-100 px-2 py-1 rounded block break-all">{{ $readableCommand }}</code>
        </div>
        @endforeach
    </div>

    <!-- MCP Inspector -->
    <div class="border rounded-lg p-4 bg-blue-50 border-blue-200">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Testing with MCP Inspector</h3>
        <p class="text-sm text-gray-700 mb-3">
            You can test and debug this MCP server using the MCP Inspector, a web-based tool for interactive testing.
        </p>
        <div class="space-y-3">
            <div>
                <p class="text-sm font-medium text-gray-900 mb-1">Option 1: Using Laravel Artisan (Recommended)</p>
                <code class="text-sm bg-white px-2 py-1 rounded block border border-blue-200">php artisan mcp:inspector tasks</code>
                <p class="text-xs text-gray-600 mt-1">This command automatically starts the inspector with the correct configuration.</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 mb-1">Option 2: Manual Setup</p>
                <div class="space-y-2 ml-4">
                    <div>
                        <p class="text-sm text-gray-700 mb-1">1. Install and run the MCP Inspector:</p>
                        <code class="text-sm bg-white px-2 py-1 rounded block border border-blue-200">npx @modelcontextprotocol/inspector</code>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 mb-1">2. Connect to this server:</p>
                        <div class="flex items-center space-x-2">
                            <code class="flex-1 text-sm bg-white px-2 py-1 rounded border border-blue-200">{{ $serverUrl }}</code>
                            <button type="button" 
                                    data-copy-text="{{ json_encode($serverUrl) }}"
                                    class="copy-button text-blue-600 hover:text-blue-800 text-sm whitespace-nowrap">
                                Copy URL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 mb-1">Authentication:</p>
                <p class="text-sm text-gray-700">
                    Make sure to include your API token in the <code class="bg-white px-1 py-0.5 rounded text-xs font-mono">Authorization</code> header as <code class="bg-white px-1 py-0.5 rounded text-xs font-mono">Bearer YOUR_TOKEN</code>. 
                    Generate your token from your Profile page.
                </p>
            </div>
        </div>
    </div>
@php
    $installationContent = ob_get_clean();
@endphp
@include('mcp-docs::partials.section-card', [
    'title' => 'Installation',
    'containerClass' => 'space-y-6',
    'content' => $installationContent,
])

<!-- Examples section -->
@php
    $examplesContent = '';
    ob_start();
@endphp
    @foreach($tools as $tool)
    @php
        $requiredParams = $tool['parameters']['required'] ?? [];
        $properties = $tool['parameters']['properties'] ?? [];
        
        // Ensure requiredParams is an array
        if (!is_array($requiredParams)) {
            $requiredParams = [];
        }
        
        $optionalParams = array_diff_key($properties, array_flip($requiredParams));
        
        // Helper function to generate example value based on schema
        $getExampleValue = function($paramName, $paramInfo) {
            $type = $paramInfo['type'] ?? 'string';
            // Handle array types (nullable fields)
            if (is_array($type)) {
                // Use the first non-null type
                $type = array_filter($type, fn($t) => $t !== 'null')[0] ?? $type[0];
            }
            $enum = $paramInfo['enum'] ?? null;
            $format = $paramInfo['format'] ?? null;
            
            if ($enum && is_array($enum)) {
                return $enum[0];
            }
            
            if ($format === 'date') {
                return '2025-12-31';
            }
            
            switch ($type) {
                case 'integer':
                    return 1;
                case 'boolean':
                    return true;
                case 'array':
                    return '[]';
                default:
                    // Generate a realistic example based on parameter name
                    if (str_contains($paramName, 'id')) {
                        return 'your-' . str_replace('_', '-', $paramName);
                    }
                    if (str_contains($paramName, 'name')) {
                        return 'My Task';
                    }
                    if (str_contains($paramName, 'description')) {
                        return 'Task description';
                    }
                    if (str_contains($paramName, 'priority')) {
                        return 'medium';
                    }
                    if (str_contains($paramName, 'filter')) {
                        return 'active';
                    }
                    if (str_contains($paramName, 'limit')) {
                        return 10;
                    }
                    if (str_contains($paramName, 'completed')) {
                        return true;
                    }
                    return 'example';
            }
        };
        
        // Build function-style examples
        $buildFunctionExample = function($paramNames, $properties) use ($tool, $getExampleValue) {
            $params = [];
            foreach ($paramNames as $paramName) {
                $paramInfo = $properties[$paramName] ?? [];
                $value = $getExampleValue($paramName, $paramInfo);
                if (is_bool($value)) {
                    $params[] = $paramName . ': ' . ($value ? 'true' : 'false');
                } elseif (is_numeric($value) && !is_string($value)) {
                    $params[] = $paramName . ': ' . $value;
                } else {
                    $params[] = $paramName . ': ' . '"' . $value . '"';
                }
            }
            return $tool['name'] . '(' . implode(', ', $params) . ')';
        };
    @endphp
    <div class="mb-4">
        <h3 class="font-medium text-gray-900 mb-2">{{ $tool['title'] }}</h3>
        <div class="bg-gray-100 p-3 rounded text-sm font-mono space-y-1">
                @if(count($properties) > 0)
                    @if(count($requiredParams) > 0)
                        @if(count($optionalParams) > 0)
                            <div class="text-gray-500"># With required and optional parameters</div>
                            <div>{{ $buildFunctionExample(array_merge($requiredParams, array_slice(array_keys($optionalParams), 0, 1)), $properties) }}</div>
                            @if(count($optionalParams) > 1)
                                <div class="text-gray-500 mt-2"># Or with more optional parameters:</div>
                                <div>{{ $buildFunctionExample(array_merge($requiredParams, array_slice(array_keys($optionalParams), 0, 2)), $properties) }}</div>
                            @endif
                        @else
                            <div class="text-gray-500"># With required parameters</div>
                            <div>{{ $buildFunctionExample($requiredParams, $properties) }}</div>
                        @endif
                @elseif(count($optionalParams) > 0)
                    <div class="text-gray-500"># All parameters are optional</div>
                    <div>{{ $tool['name'] }}()</div>
                    <div class="text-gray-500 mt-2"># Or with optional parameters:</div>
                    <div>{{ $buildFunctionExample(array_slice(array_keys($optionalParams), 0, 2), $properties) }}</div>
                @else
                    <div>{{ $tool['name'] }}()</div>
                @endif
            @else
                <div>{{ $tool['name'] }}()</div>
            @endif
        </div>
    </div>
    @endforeach
@php
    $examplesContent = ob_get_clean();
@endphp
@include('mcp-docs::partials.examples-card', [
    'title' => 'Usage Examples',
    'content' => $examplesContent,
])

@if(config('mcp-docs.support_message'))
<!-- Support section -->
@include('mcp-docs::partials.support-info', [
    'message' => config('mcp-docs.support_message'),
])
@endif
@endsection

@push('scripts')
<script>
    /**
     * Copy text to clipboard with fallback support
     * @param {string} text - The text to copy
     * @returns {Promise<boolean>} - True if successful, false otherwise
     */
    async function copyToClipboard(text) {
        // Modern Clipboard API (requires HTTPS or localhost)
        // Check if Clipboard API is available and we're in a secure context
        const isSecureContext = window.isSecureContext === true || 
                                window.location.protocol === 'https:' || 
                                window.location.hostname === 'localhost' ||
                                window.location.hostname === '127.0.0.1';
        
        if (navigator.clipboard && isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (err) {
                console.warn('Clipboard API failed, trying fallback:', err);
                // Fall through to fallback method
            }
        }
        
        // Fallback method for non-HTTPS contexts or older browsers
        try {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            
            if (!successful) {
                throw new Error('execCommand copy failed');
            }
            
            return true;
        } catch (err) {
            console.error('Fallback copy method failed:', err);
            return false;
        }
    }

    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - The type of toast ('success' or 'error')
     */
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Initialize copy buttons
    function initializeCopyButtons() {
        document.querySelectorAll('.copy-button').forEach(function(button) {
            // Skip if already initialized
            if (button.dataset.initialized === 'true') {
                return;
            }
            
            button.addEventListener('click', async function() {
                const textToCopy = JSON.parse(this.getAttribute('data-copy-text') || '""');
                
                const success = await copyToClipboard(textToCopy);
                
                if (success) {
                    showToast('Copied to clipboard!', 'success');
                } else {
                    showToast('Failed to copy to clipboard. Please copy manually.', 'error');
                }
            });
            
            // Mark as initialized
            button.dataset.initialized = 'true';
        });
    }

    // Initialize when DOM is ready (handles both cases: before and after DOMContentLoaded)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCopyButtons);
    } else {
        // DOM is already loaded
        initializeCopyButtons();
    }
</script>
@endpush

