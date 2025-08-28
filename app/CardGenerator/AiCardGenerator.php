<?php

namespace App\CardGenerator;

use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AiCardGenerator
{
    /**
     * Generates a specified number of flashcards for a given theme.
     *
     * @param  string  $theme  The theme for the flashcards.
     * @param  int  $count  The number of cards to generate.
     * @return array|null An array of generated cards or null on failure.
     */
    private string $aiMainEngine;

    public function __construct()
    {
        $this->aiMainEngine = env('AI_MAIN_ENGINE', 'gemini');
    }

    public function generate(string $theme, int $count = 10): ?array
    {
        // Simple rate limiting using cache
        $cacheKey = 'ai-request-'.md5($theme.$count);
        $rateLimitKey = 'ai-rate-limit';

        // Check rate limit (max 10 requests per minute)
        $requestCount = Cache::get($rateLimitKey, 0);
        if ($requestCount >= 10) {
            Log::warning('AI Card Generation: Rate limit exceeded');

            return null;
        }

        // Increment rate limit counter
        Cache::put($rateLimitKey, $requestCount + 1, 60); // 60 seconds

        try {
            $prompt = "
                I'm creating a flashcard application and need example cards
                Provide me **{$count}** flashcards, keep the theme being  '{$theme}'.

                Rules:
                Keep the questions and answers short, questions with 40 words at most. And answers with 10 words at most.
                Do NOT provide cards questions with multiple answers like \"Name X instaces of Y\" or \"Name a common/iconic Z\"
            ";

            try {
                switch ($this->aiMainEngine) {
                    case 'openai':
                        $prompt .= "
                            Provide the response as a JSON array of objects, where each object has a 'question' key and an 'answer' key.
                            The root of the JSON response should be the array itself. Example response:
                                [
                                    {\"question\": \"Q1\", \"answer\": \"A1\"},
                                    {\"question\": \"Q2\", \"answer\": \"A2\"},
                                    {\"question\": \"Q3\", \"answer\": \"A3\"},
                                    {\"question\": \"Q4\", \"answer\": \"A4\"}
                                    other flashcards...
                                ].
                        ";
                        $result = OpenAI::chat()->create([
                            'model' => 'gpt-4o',
                            'messages' => [['role' => 'user', 'content' => $prompt]],
                            'response_format' => ['type' => 'json_object'],
                        ]);
                        $jsonContent = $result->choices[0]->message->content;
                        break;
                    default:
                        $generationConfig = new GenerationConfig(
                            responseMimeType: ResponseMimeType::APPLICATION_JSON,
                            responseSchema: new Schema(
                                type: DataType::OBJECT,
                                properties: [
                                    'cards' => new Schema(
                                        type: DataType::ARRAY,
                                        items: new Schema(
                                            type: DataType::OBJECT,
                                            properties: [
                                                'question' => new Schema(type: DataType::STRING),
                                                'answer' => new Schema(type: DataType::STRING),
                                            ],
                                            required: ['question', 'answer']
                                        )
                                    ),
                                ]
                            )
                        );

                        $result = Gemini::generativeModel(model: 'gemini-1.5-flash-latest')
                            ->withGenerationConfig($generationConfig)
                            ->generateContent($prompt);

                        $jsonContent = $result->text();
                        break;
                }

                $decodedResponse = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('AI Card Generation: Invalid JSON response from AI service.', [
                        'error' => json_last_error_msg(),
                        'response' => $jsonContent,
                    ]);

                    return null;
                }

                $generatedCards = null;

                if (is_array($decodedResponse)) {
                    if (! empty($decodedResponse['question']) && ! empty($decodedResponse['answer'])) {
                        $generatedCards = [$decodedResponse];
                    } elseif (isset($decodedResponse['cards']) && is_array($decodedResponse['cards'])) {
                        $generatedCards = $decodedResponse['cards'];
                    } elseif (count($decodedResponse) > 0 && isset($decodedResponse[0]['question'])) {
                        $generatedCards = $decodedResponse;
                    } elseif (count($decodedResponse) === 1) {
                        $firstValue = reset($decodedResponse);
                        if (is_array($firstValue)) {
                            if (isset($firstValue['question'])) {
                                $generatedCards = [$firstValue];
                            } elseif (isset($firstValue[0]['question'])) {
                                $generatedCards = $firstValue;
                            }
                        }
                    }
                }

                if ($generatedCards === null || ! is_array($generatedCards)) {
                    Log::error('AI Card Generation: Unable to parse response structure.', [
                        'response_type' => gettype($decodedResponse),
                        'response_keys' => is_array($decodedResponse) ? array_keys($decodedResponse) : null,
                        'response' => $decodedResponse,
                    ]);

                    return null;
                }

                $validatedCards = [];
                foreach ($generatedCards as $cardData) {
                    if (! is_array($cardData) ||
                        ! isset($cardData['question']) ||
                        ! isset($cardData['answer']) ||
                        ! is_string($cardData['question']) ||
                        ! is_string($cardData['answer'])) {
                        Log::warning('AI Card Generation: Invalid card structure, skipping.', ['card' => $cardData]);

                        continue;
                    }

                    $validatedCards[] = [
                        'question' => trim(strip_tags($cardData['question'])),
                        'answer' => trim(strip_tags($cardData['answer'])),
                    ];
                }

                Log::info('AI Card Generation: Generated cards successfully.', ['count' => count($validatedCards)]);

                if (empty($validatedCards)) {
                    Log::error('AI Card Generation: No valid cards after validation.', ['response' => $jsonContent]);

                    return null;
                }

                return $validatedCards;

            } catch (\Exception $e) {
                Log::error('AI Card Generation: Failed to get a response from the AI.', ['error' => $e->getMessage()]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error('AI Card Generation: Unexpected error during generation process.', [
                'error' => $e->getMessage(),
                'theme' => $theme,
                'count' => $count,
            ]);

            return null;
        }
    }
}
