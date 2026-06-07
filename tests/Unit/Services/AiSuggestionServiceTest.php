<?php

use App\Services\AiSuggestionService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.llm.api_key', 'test-token');
    config()->set('services.llm.base_url', 'https://llm.test');
    config()->set('services.llm.model', 'test-model');
});

test('ai suggestion service sends a structured request and normalizes items', function () {
    Http::fake([
        'https://llm.test/chat/completions' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        'items' => [
                            [
                                'title' => '  Book photographer  ',
                                'description' => '  Find a photographer  ',
                                'reason' => 'Important',
                                'category' => 'Planning',
                                'priority' => 'urgent',
                                'estimated_price' => '1000 PLN',
                            ],
                            [
                                'title' => '',
                                'description' => 'Ignored because title is empty',
                            ],
                        ],
                    ]),
                ],
            ]],
        ]),
    ]);

    $items = app(AiSuggestionService::class)->suggestTasks(
        context: ['event_name' => 'Wedding'],
        guidelines: 'Keep it practical',
        limit: 5,
        locale: 'en',
    );

    expect($items)->toHaveCount(1)
        ->and($items[0]['title'])->toBe('Book photographer')
        ->and($items[0]['priority'])->toBe('medium');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://llm.test/chat/completions'
            && $request['model'] === 'test-model'
            && $request['response_format']['type'] === 'json_object'
            && str_contains($request['messages'][1]['content'], 'Wedding');
    });
});

test('ai suggestion service returns empty array for empty or invalid response content', function (array $payload) {
    Http::fake([
        'https://llm.test/chat/completions' => Http::response($payload),
    ]);

    expect(app(AiSuggestionService::class)->suggestTasks())->toBe([]);
})->with([
    'empty content' => [[
        'choices' => [['message' => ['content' => '']]],
    ]],
    'invalid json' => [[
        'choices' => [['message' => ['content' => 'not-json']]],
    ]],
]);

test('ai suggestion service throws an exception on api failure', function () {
    Http::fake([
        'https://llm.test/chat/completions' => Http::response('Service unavailable', 503),
    ]);

    expect(fn () => app(AiSuggestionService::class)->suggestGifts())
        ->toThrow(RuntimeException::class, 'LLM API error');
});

test('ai suggestion service filters exact duplicates but currently allows compound similar names', function () {
    $service = app(AiSuggestionService::class);

    expect($service->filterGiftNames(
        names: ['Camera', 'camera', 'Wedding camera set', 'Board game'],
        existingNames: ['Camera'],
        limit: 10,
    ))->toBe(['Wedding camera set', 'Board game']);
});
