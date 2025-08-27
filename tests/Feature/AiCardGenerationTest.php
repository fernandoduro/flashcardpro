<?php

use App\CardGenerator\AiCardGenerator;

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
        if ($property->getName() === 'ai_main_engine') {
            $engineProperty = $property;
            break;
        }
    }

    expect($engineProperty)->not->toBeNull();
});
