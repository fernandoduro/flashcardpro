<?php

namespace App\CardGenerator;

use Gemini\Laravel\Facades\Gemini;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;

class AiCardGenerator
{
    /**
     * Generates a specified number of flashcards for a given theme.
     *
     * @param  string  $theme The theme for the flashcards.
     * @param  int  $count The number of cards to generate.
     * @return array|null An array of generated cards or null on failure.
     */

    public $ai_main_engine;

    public function __construct()
    {
        $this->ai_main_engine = env('AI_MAIN_ENGINE', 'gemini'); // or 'openai' (experimental)
    }

    public function generate(string $theme, int $count = 10): ?array
    {
        // Simple rate limiting using cache
        $cacheKey = "ai-request-" . md5($theme . $count);
        $rateLimitKey = "ai-rate-limit";

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
                Keep the questions and answers short, with 20 words at most.
                Do NOT provide cards questions with multiple answers like \"Name X instaces of Y\" or \"Name a common/iconic Z\"
            ";

            try {
                switch ($this->ai_main_engine) {
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
                                type: DataType::OBJECT, // The root is an object
                                properties: [
                                    'cards' => new Schema( // It has a key called 'cards'
                                        type: DataType::ARRAY, // The value is an array
                                        items: new Schema( // The items in the array are objects
                                            type: DataType::OBJECT,
                                            properties: [
                                                'question' => new Schema(type: DataType::STRING),
                                                'answer' => new Schema(type: DataType::STRING)
                                            ],
                                            required: ['question', 'answer']
                                        )
                                    )
                                ]
                            )
                        );

                        // 3. Make the API call using the correct, modern syntax
                        $result = Gemini::generativeModel(model: 'gemini-1.5-flash-latest') // Use a modern, valid model name
                            ->withGenerationConfig($generationConfig)
                            ->generateContent($prompt);

                        // 4. The SDK can directly return the parsed JSON object/array
                        $jsonContent = $result->text();
                        break;
                }

                // Safely decode JSON with proper error handling
                $decodedResponse = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('AI Card Generation: Invalid JSON response from AI service.', [
                        'error' => json_last_error_msg(),
                        'response' => $jsonContent
                    ]);
                    return null;
                }

                // Handle different response structures
                if (!empty($decodedResponse['question'])) {
                    // If the response is a single object instead of an array
                    $generatedCards = [$decodedResponse];
                } elseif (isset($decodedResponse['cards']) && is_array($decodedResponse['cards'])) {
                    // If response has a 'cards' wrapper
                    $generatedCards = $decodedResponse['cards'];
                } elseif (is_array($decodedResponse) && !empty($decodedResponse)) {
                    // If response is directly an array
                    $firstKey = array_key_first($decodedResponse);
                    $generatedCards = is_array($decodedResponse[$firstKey])
                        ? $decodedResponse[$firstKey]
                        : $decodedResponse;
                } else {
                    Log::error('AI Card Generation: Unexpected response structure.', ['response' => $decodedResponse]);
                    return null;
                }

                // Validate and sanitize each card
                $validatedCards = [];
                foreach ($generatedCards as $cardData) {
                    if (!is_array($cardData) ||
                        !isset($cardData['question']) ||
                        !isset($cardData['answer']) ||
                        !is_string($cardData['question']) ||
                        !is_string($cardData['answer'])) {
                        Log::warning('AI Card Generation: Invalid card structure, skipping.', ['card' => $cardData]);
                        continue;
                    }

                    // Sanitize content
                    $validatedCards[] = [
                        'question' => trim(strip_tags($cardData['question'])),
                        'answer' => trim(strip_tags($cardData['answer']))
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
                'count' => $count
            ]);
            return null;
        }
    }
}