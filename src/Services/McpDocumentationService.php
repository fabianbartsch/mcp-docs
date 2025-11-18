<?php

declare(strict_types=1);

namespace FabianBartsch\McpDocs\Services;

use Illuminate\Contracts\Container\Container;
use ReflectionClass;

/**
 * Service for extracting MCP server documentation metadata.
 */
class McpDocumentationService
{
    public function __construct(
        protected Container $container
    ) {}

    /**
     * Get all tools from the MCP server with their metadata.
     *
     * @param  class-string  $serverClass
     * @return array<string, array{name: string, title: string, description: string, parameters: array<string, mixed>}>
     */
    public function getTools(string $serverClass): array
    {
        $tools = [];
        $reflection = new ReflectionClass($serverClass);
        $defaultProperties = $reflection->getDefaultProperties();
        $toolClasses = $defaultProperties['tools'] ?? [];

        if (! is_array($toolClasses)) {
            return $tools;
        }

        foreach ($toolClasses as $toolClass) {
            try {
                if (! is_string($toolClass) || ! class_exists($toolClass)) {
                    continue;
                }

                $toolInstance = $this->container->make($toolClass);
                $toolReflection = new ReflectionClass($toolInstance);

                $name = $this->getPropertyValue($toolReflection, $toolInstance, 'name');
                $title = $this->getPropertyValue($toolReflection, $toolInstance, 'title');
                $description = $this->getPropertyValue($toolReflection, $toolInstance, 'description');

                // If name is not set, derive it from the class name (Laravel MCP convention)
                if ($name === null || $name === '') {
                    $className = $toolReflection->getShortName();
                    // Remove "Tool" suffix if present (e.g., "CreateTaskTool" -> "create_task")
                    $name = str_replace('Tool', '', $className);
                    // Convert PascalCase to snake_case
                    $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
                }
                
                // If title is not set, use a human-readable version of the name
                if ($title === null || $title === '') {
                    $title = str_replace('_', ' ', $name);
                    $title = ucwords($title);
                }

                $parameters = [];
                // Laravel MCP uses 'schema' method, not 'inputSchema'
                if (method_exists($toolInstance, 'schema')) {
                    try {
                        $schemaMethod = $toolReflection->getMethod('schema');
                        $schemaParams = $schemaMethod->getParameters();
                        if (count($schemaParams) > 0 && $schemaParams[0]->getType()?->getName() === 'Illuminate\JsonSchema\JsonSchema') {
                            // Convert JsonSchema objects to JSON schema array format
                            // This matches how Laravel MCP's Tool::toArray() does it
                            $schemaArray = \Illuminate\JsonSchema\JsonSchema::object(
                                fn (\Illuminate\JsonSchema\JsonSchema $schema) => $toolInstance->schema($schema)
                            )->toArray();
                            $parameters = $schemaArray;
                        }
                    } catch (\Throwable $e) {
                        // If we can't call schema, try inputSchema as fallback
                        if (method_exists($toolInstance, 'inputSchema')) {
                            $parameters = $toolInstance->inputSchema();
                        }
                    }
                } elseif (method_exists($toolInstance, 'inputSchema')) {
                    $parameters = $toolInstance->inputSchema();
                }

                $tools[$name] = [
                    'name' => $name,
                    'title' => $title ?? $name,
                    'description' => $description ?? '',
                    'parameters' => is_array($parameters) ? $parameters : [],
                ];
            } catch (\Throwable $e) {
                // Skip tools that cannot be instantiated or accessed
                continue;
            }
        }

        return $tools;
    }

    /**
     * Get all resources from the MCP server with their metadata.
     *
     * @param  class-string  $serverClass
     * @return array<string, array{name: string, title: string, description: string, uri: string|null, mimeType: string|null}>
     */
    public function getResources(string $serverClass): array
    {
        $resources = [];
        $reflection = new ReflectionClass($serverClass);
        $defaultProperties = $reflection->getDefaultProperties();
        $resourceClasses = $defaultProperties['resources'] ?? [];

        if (! is_array($resourceClasses)) {
            return $resources;
        }

        foreach ($resourceClasses as $resourceClass) {
            try {
                if (! is_string($resourceClass) || ! class_exists($resourceClass)) {
                    continue;
                }

                $resourceInstance = $this->container->make($resourceClass);
                $resourceReflection = new ReflectionClass($resourceInstance);

                $name = $this->getPropertyValue($resourceReflection, $resourceInstance, 'name');
                $title = $this->getPropertyValue($resourceReflection, $resourceInstance, 'title');
                $description = $this->getPropertyValue($resourceReflection, $resourceInstance, 'description');

                // If name is not set, derive it from the class name (Laravel MCP convention)
                if ($name === null || $name === '') {
                    $className = $resourceReflection->getShortName();
                    // Remove "Resource" suffix if present
                    $name = str_replace('Resource', '', $className);
                    // Convert PascalCase to snake_case
                    $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
                }
                
                // If title is not set, use a human-readable version of the name
                if ($title === null || $title === '') {
                    $title = str_replace('_', ' ', $name);
                    $title = ucwords($title);
                }

                $uri = $this->getPropertyValue($resourceReflection, $resourceInstance, 'uri');
                $mimeType = $this->getPropertyValue($resourceReflection, $resourceInstance, 'mimeType');

                $resources[$name] = [
                    'name' => $name,
                    'title' => $title ?? $name,
                    'description' => $description ?? '',
                    'uri' => $uri,
                    'mimeType' => $mimeType,
                ];
            } catch (\Throwable $e) {
                // Skip resources that cannot be instantiated or accessed
                continue;
            }
        }

        return $resources;
    }

    /**
     * Get all prompts from the MCP server with their metadata.
     *
     * @param  class-string  $serverClass
     * @return array<string, array{name: string, title: string, description: string, arguments: array<string, mixed>}>
     */
    public function getPrompts(string $serverClass): array
    {
        $prompts = [];
        $reflection = new ReflectionClass($serverClass);
        $defaultProperties = $reflection->getDefaultProperties();
        $promptClasses = $defaultProperties['prompts'] ?? [];

        if (! is_array($promptClasses)) {
            return $prompts;
        }

        foreach ($promptClasses as $promptClass) {
            try {
                if (! is_string($promptClass) || ! class_exists($promptClass)) {
                    continue;
                }

                $promptInstance = $this->container->make($promptClass);
                $promptReflection = new ReflectionClass($promptInstance);

                $name = $this->getPropertyValue($promptReflection, $promptInstance, 'name');
                $title = $this->getPropertyValue($promptReflection, $promptInstance, 'title');
                $description = $this->getPropertyValue($promptReflection, $promptInstance, 'description');

                // If name is not set, derive it from the class name (Laravel MCP convention)
                if ($name === null || $name === '') {
                    $className = $promptReflection->getShortName();
                    // Remove "Prompt" suffix if present
                    $name = str_replace('Prompt', '', $className);
                    // Convert PascalCase to snake_case
                    $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
                }
                
                // If title is not set, use a human-readable version of the name
                if ($title === null || $title === '') {
                    $title = str_replace('_', ' ', $name);
                    $title = ucwords($title);
                }

                $arguments = [];
                if (method_exists($promptInstance, 'arguments')) {
                    $argumentsResult = $promptInstance->arguments();
                    $arguments = is_array($argumentsResult) ? $argumentsResult : [];
                }

                $prompts[$name] = [
                    'name' => $name,
                    'title' => $title ?? $name,
                    'description' => $description ?? '',
                    'arguments' => $arguments,
                ];
            } catch (\Throwable $e) {
                // Skip prompts that cannot be instantiated or accessed
                continue;
            }
        }

        return $prompts;
    }

    /**
     * Get server metadata.
     *
     * @param  class-string  $serverClass
     * @return array{name: string, version: string, instructions: string}
     */
    public function getServerInfo(string $serverClass): array
    {
        try {
            $reflection = new ReflectionClass($serverClass);
            $defaultProperties = $reflection->getDefaultProperties();

            return [
                'name' => $defaultProperties['name'] ?? 'MCP Server',
                'version' => $defaultProperties['version'] ?? '0.0.1',
                'instructions' => $defaultProperties['instructions'] ?? '',
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'MCP Server',
                'version' => '0.0.1',
                'instructions' => '',
            ];
        }
    }

    /**
     * Safely get a property value from a reflected class instance.
     *
     * @param  ReflectionClass  $reflection
     * @param  object  $instance
     * @param  string  $propertyName
     * @return mixed
     */
    protected function getPropertyValue(ReflectionClass $reflection, object $instance, string $propertyName): mixed
    {
        try {
            if (! $reflection->hasProperty($propertyName)) {
                return null;
            }

            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);

            return $property->getValue($instance);
        } catch (\Throwable $e) {
            return null;
        }
    }
}

