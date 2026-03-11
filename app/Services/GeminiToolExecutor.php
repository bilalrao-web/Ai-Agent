<?php

namespace App\Services;

class GeminiToolExecutor
{
    public function __construct(
        protected int $customerId,
        protected McpToolRunner $mcpToolRunner
    ) {}

    /**
     * Execute a tool by name with the given args via MCP. Returns a JSON-serializable array for Gemini.
     */
    public function execute(string $name, array $args): array
    {
        $mcpToolName = McpToolRunner::geminiToMcpToolName($name);
        $mcpArgs = $this->buildMcpArgs($name, $args);

        $result = $this->mcpToolRunner->run($mcpToolName, $mcpArgs);

        return $result;
    }

    /**
     * Build MCP tool arguments from Gemini tool name and args (including customer_id where needed).
     *
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    protected function buildMcpArgs(string $geminiToolName, array $args): array
    {
        return match ($geminiToolName) {
            'get_latest_order' => ['customer_id' => $this->customerId],
            'get_order_by_number' => [
                'customer_id' => $this->customerId,
                'order_number' => (string) ($args['order_number'] ?? ''),
            ],
            'get_open_tickets' => ['customer_id' => $this->customerId],
            'create_ticket' => [
                'customer_id' => $this->customerId,
                'issue_type' => (string) ($args['issue_type'] ?? 'General'),
                'description' => (string) ($args['description'] ?? ''),
            ],
            'search_faq' => ['query' => (string) ($args['query'] ?? '')],
            default => array_merge(['customer_id' => $this->customerId], $args),
        };
    }
}
