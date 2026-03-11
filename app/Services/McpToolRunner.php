<?php

namespace App\Services;

use Illuminate\Support\Str;
use JsonException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Server\Tool as McpTool;
use Laravel\Boost\Mcp\ToolRegistry;

class McpToolRunner
{
    /** @var array<string, class-string<McpTool>>|null */
    private ?array $nameToClass = null;

    /**
     * Run an MCP tool by name with the given arguments. Returns a JSON-serializable array for Gemini.
     *
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    public function run(string $mcpToolName, array $arguments): array
    {
        $class = $this->getToolClassByName($mcpToolName);
        if ($class === null) {
            return ['error' => "Unknown MCP tool: {$mcpToolName}"];
        }

        $request = new Request($arguments);

        try {
            /** @var McpTool $tool */
            $tool = app()->make($class);
            $response = $tool->handle($request);
        } catch (\Throwable $e) {
            report($e);
            return ['error' => $e->getMessage()];
        }

        return $this->responseToArray($response);
    }

    /**
     * Get MCP tool name (kebab-case) for a given Gemini tool name. Used to map Gemini function names to MCP tools.
     */
    public static function geminiToMcpToolName(string $geminiToolName): string
    {
        return match ($geminiToolName) {
            'get_latest_order' => 'get-latest-order',
            'get_order_by_number' => 'get-order-by-number',
            'get_open_tickets' => 'get-open-tickets',
            'create_ticket' => 'create-ticket',
            'search_faq' => 'search-faq',
            default => Str::replace('_', '-', $geminiToolName),
        };
    }

    /**
     * @return class-string<McpTool>|null
     */
    private function getToolClassByName(string $mcpToolName): ?string
    {
        $map = $this->getNameToClassMap();
        return $map[$mcpToolName] ?? null;
    }

    /**
     * @return array<string, class-string<McpTool>>
     */
    private function getNameToClassMap(): array
    {
        if ($this->nameToClass !== null) {
            return $this->nameToClass;
        }

        $map = [];
        foreach (ToolRegistry::getAvailableTools() as $toolClass) {
            if (! is_subclass_of($toolClass, McpTool::class)) {
                continue;
            }
            /** @var McpTool $tool */
            $tool = app()->make($toolClass);
            $map[$tool->name()] = $toolClass;
        }

        $this->nameToClass = $map;
        return $map;
    }

    /**
     * @return array<string, mixed>
     */
    private function responseToArray(\Laravel\Mcp\Response $response): array
    {
        $text = (string) $response->content();
        if ($text === '') {
            return ['result' => null];
        }
        try {
            $decoded = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : ['text' => $text];
        } catch (JsonException) {
            return ['text' => $text];
        }
    }
}
