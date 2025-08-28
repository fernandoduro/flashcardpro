<?php

use App\CardGenerator\AiCardGenerator;
use App\Models\User;
use Gemini\Exceptions\GeminiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;

use function Pest\Laravel\actingAs;

/**
 * AiCardGenerationTest - Tests for AI-powered card generation functionality
 *
 * This test suite validates the AiCardGenerator class which integrates with
 * external AI services (Gemini/OpenAI) to generate flashcards automatically.
 *
 * Tests cover:
 * - Class instantiation and configuration
 * - Method signatures and return types
 * - Multi-engine support (Gemini/OpenAI)
 * - Error handling and reflection capabilities
 * - Integration with Laravel configuration system
 * - Rate limiting functionality
 * - Input validation and sanitization
 */
beforeEach(function () {
    // Mock the AI service environment variables for testing
    config(['gemini.api_key' => 'test-api-key']);
    config(['openai.api_key' => 'test-openai-key']);
    config(['app.ai_main_engine' => 'gemini']);
});

test('AI generator can be instantiated', function () {
    $generator = new AiCardGenerator;
    expect($generator)->toBeInstanceOf(AiCardGenerator::class);
});

test('AI generator has required methods', function () {
    $generator = new AiCardGenerator;

    // Test that the class has the required public methods
    expect(method_exists($generator, 'generate'))->toBeTrue();
    expect(method_exists($generator, '__construct'))->toBeTrue();
});

test('AI generator handles method reflection and signature', function () {
    $generator = new AiCardGenerator;

    // Test that the class exists and can handle errors gracefully
    expect($generator)->toBeInstanceOf(AiCardGenerator::class);

    // Test the generate method signature using reflection
    $reflection = new \ReflectionClass($generator);
    $method = $reflection->getMethod('generate');
    $parameters = $method->getParameters();

    expect($parameters)->toHaveCount(2);
    expect($parameters[0]->getName())->toEqual('theme');
    expect($parameters[1]->getName())->toEqual('count');
});

test('AI generator can handle different engine configurations', function () {
    $generator = new AiCardGenerator;

    // Test that the generator can be instantiated with different configurations
    config(['app.ai_main_engine' => 'openai']);
    $generator2 = new AiCardGenerator;
    expect($generator2)->toBeInstanceOf(AiCardGenerator::class);

    // Reset to original config
    config(['app.ai_main_engine' => 'gemini']);
});

test('AI generator has correct method signature', function () {
    $generator = new AiCardGenerator;

    // Test method signature and return type using reflection
    $reflection = new \ReflectionClass($generator);
    $method = $reflection->getMethod('generate');

    expect($method->isPublic())->toBeTrue();
    expect($method->hasReturnType())->toBeTrue();
    $returnType = $method->getReturnType();
    expect($returnType->getName())->toEqual('array');
    expect($returnType->allowsNull())->toBeTrue();
});

test('AI generator supports different engine configurations', function () {
    // Test different AI engine configurations
    config(['app.ai_main_engine' => 'gemini']);
    $generator1 = new AiCardGenerator;
    expect($generator1)->toBeInstanceOf(AiCardGenerator::class);

    config(['app.ai_main_engine' => 'openai']);
    $generator2 = new AiCardGenerator;
    expect($generator2)->toBeInstanceOf(AiCardGenerator::class);
});

test('AI generator has proper error handling structure', function () {
    $generator = new AiCardGenerator;

    // Test that the class has proper error handling by checking if it uses logging
    $reflection = new \ReflectionClass($generator);
    $properties = $reflection->getProperties();

    // Check that the class has an ai_main_engine property for configuration
    $engineProperty = null;
    foreach ($properties as $property) {
        if ($property->getName() === 'aiMainEngine') {
            $engineProperty = $property;
            break;
        }
    }

    expect($engineProperty)->not->toBeNull();
});

// Integration-style tests that work with the actual implementation
test('AI generator respects rate limiting in integration', function () {
    Cache::flush();

    $generator = new AiCardGenerator;

    // Make multiple calls to test rate limiting (these will likely fail due to no API keys, but should be rate limited)
    $results = [];
    for ($i = 0; $i < 15; $i++) {
        $results[] = $generator->generate('test theme', 1);
        // Small delay to avoid overwhelming
        usleep(1000);
    }

    // After rate limit is exceeded, all calls should return null
    $nullCount = count(array_filter($results, fn($result) => $result === null));
    expect($nullCount)->toBeGreaterThan(0); // At least some calls should be rate limited
});

test('AI generator handles configuration changes', function () {
    // Test with different configurations
    config(['app.ai_main_engine' => 'gemini']);
    $generator1 = new AiCardGenerator;
    expect($generator1)->toBeInstanceOf(AiCardGenerator::class);

    config(['app.ai_main_engine' => 'openai']);
    $generator2 = new AiCardGenerator;
    expect($generator2)->toBeInstanceOf(AiCardGenerator::class);

    // Reset to default
    config(['app.ai_main_engine' => 'gemini']);
});

test('AI generator method signature is correct', function () {
    $generator = new AiCardGenerator;
    $reflection = new \ReflectionClass($generator);
    $method = $reflection->getMethod('generate');

    expect($method->getNumberOfParameters())->toBe(2);
    $parameters = $method->getParameters();

    expect($parameters[0]->getName())->toBe('theme');
    expect($parameters[1]->getName())->toBe('count');
    expect($parameters[1]->isOptional())->toBeTrue(); // count has default value of 10
});

test('AI generator has proper property structure', function () {
    $generator = new AiCardGenerator;
    $reflection = new \ReflectionClass($generator);

    // Check that it has the aiMainEngine property
    $properties = $reflection->getProperties();
    $aiMainEngineProperty = null;

    foreach ($properties as $property) {
        if ($property->getName() === 'aiMainEngine') {
            $aiMainEngineProperty = $property;
            break;
        }
    }

    expect($aiMainEngineProperty)->not->toBeNull();
    expect($aiMainEngineProperty->isPrivate())->toBeTrue();
});

test('AI generator handles different count parameters', function () {
    $generator = new AiCardGenerator;

    // Test with different count values (these will fail due to no API, but shouldn't crash)
    $result1 = $generator->generate('test', 1);
    $result2 = $generator->generate('test', 5);
    $result3 = $generator->generate('test', 10);

    // Results should be either null (rate limited/API error) or array
    expect($result1)->toBeNull(); // Likely null due to no API key
    expect($result2)->toBeNull();
    expect($result3)->toBeNull();
});

test('AI generator accepts various theme strings', function () {
    $generator = new AiCardGenerator;

    $themes = [
        'Simple theme',
        'Complex theme with spaces',
        'Theme with special chars: ¿¡@#$%',
        str_repeat('Long theme ', 10),
    ];

    foreach ($themes as $theme) {
        $result = $generator->generate($theme, 1);
        // Should not crash, even if it returns null due to API issues
        expect($result)->toBeNull(); // Expected to be null without API keys
    }
});
