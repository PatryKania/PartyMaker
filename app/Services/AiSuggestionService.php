<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AiSuggestionService
{
    public function suggest(
        string $type,
        array $context = [],
        ?string $guidelines = null,
        int $limit = 8,
        ?string $locale = null,
    ): array {
        $limit = max(1, min($limit, 20));
        $locale ??= app()->currentLocale();

        $response = Http::withToken((string) config('services.llm.api_key'))
            ->acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout(60)
            ->retry(1, 1000)
            ->post(rtrim((string) config('services.llm.base_url'), '/') . '/chat/completions', [
                'model' => config('services.llm.model'),

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt($locale),
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'type' => $type,
                            'limit' => $limit,
                            'locale' => $locale,
                            'language' => $this->languageName($locale),
                            'guidelines' => $guidelines,
                            'context' => $context,
                        ], JSON_UNESCAPED_UNICODE),
                    ],
                ],

                'response_format' => [
                    'type' => 'json_object',
                ],

                'temperature' => 0.2,
                'max_tokens' => 1200,
                'stream' => false,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('LLM API error: ' . $response->body());
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            return [];
        }

        try {
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return [];
        }

        return collect($json['items'] ?? [])
            ->take($limit)
            ->map(fn (array $item): array => [
                'title' => Str::of((string) ($item['title'] ?? ''))->squish()->limit(150)->toString(),
                'description' => Str::of((string) ($item['description'] ?? ''))->squish()->limit(800)->toString(),
                'reason' => Str::of((string) ($item['reason'] ?? ''))->squish()->limit(500)->toString(),
                'category' => Str::of((string) ($item['category'] ?? ''))->squish()->limit(80)->toString(),
                'priority' => $this->normalizePriority($item['priority'] ?? null),
                'estimated_price' => Str::of((string) ($item['estimated_price'] ?? ''))->squish()->limit(80)->toString(),
            ])
            ->filter(fn (array $item): bool => filled($item['title']))
            ->values()
            ->all();
    }

    public function suggestTasks(
        array $context = [],
        ?string $guidelines = null,
        int $limit = 8,
        ?string $locale = null,
    ): array {
        return $this->suggest(
            type: 'tasks',
            context: $context,
            guidelines: $guidelines,
            limit: $limit,
            locale: $locale,
        );
    }

    public function suggestGifts(
        array $context = [],
        ?string $guidelines = null,
        int $limit = 8,
        ?string $locale = null,
    ): array {
        return $this->suggest(
            type: 'gifts',
            context: $context,
            guidelines: $guidelines,
            limit: $limit,
            locale: $locale,
        );
    }

    public function suggestTaskTitles(
        array $context = [],
        array $existingTitles = [],
        ?string $guidelines = null,
        int $limit = 10,
        ?string $locale = null,
    ): array {
        $limit = max(1, min($limit, 20));
        $existingTitles = $this->cleanNames($existingTitles);

        $guidelines = trim(($guidelines ?: '') . ' ' . implode(' ', [
            'Return task titles only in the title field.',
            'Suggest only new tasks.',
            'Avoid tasks that already exist or are very similar to existing_task_titles.',
            'Use short, practical task names.',
        ]));

        $suggestions = $this->suggestTasks(
            context: [
                ...$context,
                'existing_task_titles' => $existingTitles,
                'avoid_existing_tasks' => true,
                'expected_output' => 'task_titles_only',
            ],
            guidelines: $guidelines,
            limit: $limit,
            locale: $locale,
        );

        $titles = collect($suggestions)
            ->map(fn (array $suggestion): string => trim((string) ($suggestion['title'] ?? '')))
            ->filter()
            ->values()
            ->all();

        return $this->filterTaskTitles(
            titles: $titles,
            existingTitles: $existingTitles,
            limit: $limit,
        );
    }

    public function suggestGiftNames(
        array $context = [],
        array $existingNames = [],
        ?string $guidelines = null,
        int $limit = 10,
        ?string $locale = null,
    ): array {
        $limit = max(1, min($limit, 20));
        $existingNames = $this->cleanNames($existingNames);

        $guidelines = trim(($guidelines ?: '') . ' ' . implode(' ', [
            'Return gift names only in the title field.',
            'Suggest only new gifts.',
            'Avoid gifts that already exist or are very similar to existing_gift_names.',
            'Use short, practical gift names.',
            'Consider all recipient details from user_short_description.',
        ]));

        $suggestions = $this->suggestGifts(
            context: [
                ...$context,
                'existing_gift_names' => $existingNames,
                'avoid_existing_gifts' => true,
                'expected_output' => 'gift_names_only',
            ],
            guidelines: $guidelines,
            limit: $limit,
            locale: $locale,
        );

        $names = collect($suggestions)
            ->map(fn (array $suggestion): string => trim((string) ($suggestion['title'] ?? '')))
            ->filter()
            ->values()
            ->all();

        return $this->filterGiftNames(
            names: $names,
            existingNames: $existingNames,
            limit: $limit,
        );
    }

    public function filterTaskTitles(
        array $titles,
        array $existingTitles = [],
        int $limit = 10,
    ): array {
        return $this->filterNames(
            names: $titles,
            existingNames: $existingTitles,
            limit: $limit,
        );
    }

    public function filterGiftNames(
        array $names,
        array $existingNames = [],
        int $limit = 10,
    ): array {
        return $this->filterNames(
            names: $names,
            existingNames: $existingNames,
            limit: $limit,
        );
    }

    private function filterNames(
        array $names,
        array $existingNames = [],
        int $limit = 10,
    ): array {
        $limit = max(1, min($limit, 20));
        $existingNames = $this->cleanNames($existingNames);

        return collect($names)
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->reject(fn (string $name): bool => $this->isSimilarName($name, $existingNames))
            ->unique(fn (string $name): string => $this->normalizeName($name))
            ->take($limit)
            ->values()
            ->all();
    }

    private function cleanNames(array $names): array
    {
        return collect($names)
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->unique(fn (string $name): string => $this->normalizeName($name))
            ->values()
            ->all();
    }

    private function systemPrompt(string $locale): string
    {
        $language = $this->languageName($locale);

        return <<<PROMPT
You are an AI assistant in an event/project management application.

Always return only valid JSON. Do not add markdown or text outside JSON.

Write all user-facing values in this language: {$language}.
Locale: {$locale}.

JSON format:
{
  "items": [
    {
      "title": "short name",
      "description": "",
      "reason": "",
      "category": "",
      "priority": "low|medium|high",
      "estimated_price": ""
    }
  ]
}

Rules:
- Return no more items than the provided limit.
- For type "tasks", suggest concrete task titles.
- For type "gifts", suggest practical gift names.
- If expected_output is "task_titles_only", put only short task names in "title".
- If expected_output is "gift_names_only", put only short gift names in "title".
- For gifts, consider age, relationship, interests, occasion, dislikes, budget and user_short_description.
- Avoid items similar to existing_task_titles or existing_gift_names.
- Do not invent personal data.
- Keep titles short and useful.
PROMPT;
    }

    private function languageName(string $locale): string
    {
        return match (strtolower(str_replace('_', '-', $locale))) {
            'pl', 'pl-pl' => 'Polish',
            'en', 'en-us', 'en-gb' => 'English',
            default => 'English',
        };
    }

    private function normalizePriority(mixed $priority): string
    {
        return in_array($priority, ['low', 'medium', 'high'], true)
            ? $priority
            : 'medium';
    }

    private function isSimilarName(string $candidate, array $existingNames): bool
    {
        $candidate = $this->normalizeName($candidate);

        if ($candidate === '') {
            return true;
        }

        foreach ($existingNames as $existingName) {
            $existing = $this->normalizeName((string) $existingName);

            if ($existing === '') {
                continue;
            }

            if ($candidate === $existing) {
                return true;
            }

            if (
                mb_strlen($candidate) >= 8 &&
                mb_strlen($existing) >= 8 &&
                (
                    Str::contains($candidate, $existing) ||
                    Str::contains($existing, $candidate)
                )
            ) {
                return true;
            }

            similar_text($candidate, $existing, $percent);

            if ($percent >= 72) {
                return true;
            }

            if ($this->tokenSimilarity($candidate, $existing) >= 0.65) {
                return true;
            }
        }

        return false;
    }

    private function normalizeName(string $name): string
    {
        $name = Str::of($name)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]/', ' ')
            ->squish()
            ->toString();

        $stopWords = [
            'i',
            'oraz',
            'or',
            'and',
            'the',
            'a',
            'an',
            'of',
            'for',
            'na',
            'do',
            'w',
            'we',
            'z',
            'ze',
            'dla',
            'to',
            'with',
            'task',
            'zadanie',
            'gift',
            'prezent',
            'prezenty',
        ];

        return collect(explode(' ', $name))
            ->reject(fn (string $word): bool => in_array($word, $stopWords, true))
            ->implode(' ');
    }

    private function tokenSimilarity(string $first, string $second): float
    {
        $firstWords = array_values(array_unique(array_filter(explode(' ', $first))));
        $secondWords = array_values(array_unique(array_filter(explode(' ', $second))));

        if ($firstWords === [] || $secondWords === []) {
            return 0;
        }

        $intersection = count(array_intersect($firstWords, $secondWords));
        $union = count(array_unique(array_merge($firstWords, $secondWords)));

        return $union > 0
            ? $intersection / $union
            : 0;
    }
}