<?php

namespace App\Mcp\Tools;

use App\Models\Faq;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class SearchFaq extends Tool
{
    protected string $description = 'Search FAQ for common questions like working hours, return policy, how to track order, how to create a ticket. Use for general support questions.';

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Search query or topic (e.g. working hours, return policy).')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $query = (string) $request->get('query', '');

        $faqs = Faq::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('question', 'like', "%{$query}%")
                    ->orWhere('answer', 'like', "%{$query}%");
            })
            ->get(['question', 'answer']);

        if ($faqs->isEmpty()) {
            return Response::json(['found' => false, 'message' => 'No matching FAQ found.']);
        }

        return Response::json([
            'found' => true,
            'faqs' => $faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])->toArray(),
        ]);
    }
}
