# Test Suite Documentation

## Overview

This test suite provides comprehensive coverage for the FlashcardPro application, including unit tests, feature tests, integration tests, and performance tests.

## Test Structure

### Directory Organization

```
tests/
├── Feature/             # Feature Tests (HTTP, Livewire)
│   ├── Api/             # API Endpoint Tests
│   ├── Auth/            # Authentication Tests
│   ├── Livewire/        # Livewire Component Tests
│   ├── Integration/     # Complex User Workflow Tests
│   ├── Performance/     # Performance & Load Tests
│   ├── AiCardGenerationTest.php
│   ├── ProfileTest.php
│   └── DeckManagementTest.php
├── Unit/                 # Unit Tests
│   ├── V1/             # API Request Validation Tests
│   ├── Policies/       # Authorization Policy Tests
│   ├── Resources/      # API Resource Tests
│   └── ExampleTest.php
├── TestCase.php         # Feature Test Base Class
├── Pest.php             # Pest Configuration
└── README.md            # This file
```

## Test Categories

### 1. Unit Tests (`tests/Unit/`)

**Purpose:** Test individual components in isolation
- **Request Tests:** Validate form request validation rules and authorization
- **Policy Tests:** Verify authorization logic for models
- **Resource Tests:** Test API resource transformations

### 2. Feature Tests (`tests/Feature/`)

**Purpose:** Test complete features and user interactions
- **API Tests:** Test REST endpoints with authentication and validation
- **Authentication Tests:** User registration, login, password management
- **Livewire Tests:** Component interactions and reactive behavior
- **Integration Tests:** Complex user workflows from end-to-end

### 3. Performance Tests (`tests/Feature/Performance/`)

**Purpose:** Ensure application performs well under load
- **API Response Times:** Critical endpoints respond quickly
- **Large Dataset Handling:** Pagination and query optimization
- **Memory Usage:** Prevent memory leaks with large datasets

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test Suites
```bash
# Unit tests only
php artisan test tests/Unit

# Feature tests only
php artisan test tests/Feature

# Specific test file
php artisan test tests/Feature/Api/StudyApiTest.php
```

### With Coverage
```bash
php artisan test --coverage
```

### Parallel Testing
```bash
php artisan test --parallel
```

## Test Framework Configuration

### Pest PHP
- **Configuration:** `tests/Pest.php`
- **Features:**
  - Global test functions
  - Custom expectations
  - Database refreshing
  - Parallel test execution

## Test Data

### Factories
- **UserFactory:** Creates test users with optional attributes
- **DeckFactory:** Creates decks with user association
- **CardFactory:** Creates flashcards with deck association
- **StudyFactory:** Creates study sessions
- **StudyResultFactory:** Creates study result records

### Seeders
- **DatabaseSeeder:** Populates test database with sample data
- Use `php artisan db:seed` to populate test database

## Best Practices

### Naming Conventions
- **Unit Tests:** `test_method_name_description`
- **Feature Tests:** `test_user_can_action_description`

### Test Structure
```php
test('user can create a deck', function () {
    // Arrange: Set up test data
    $user = User::factory()->create();

    // Act: Perform the action
    actingAs($user);
    $response = $this->postJson('/api/v1/decks', [
        'name' => 'Test Deck'
    ]);

    // Assert: Verify the outcome
    $response->assertStatus(201)
             ->assertJsonStructure(['success', 'data']);
});
```

### Database Testing
- Use `RefreshDatabase` trait for clean test state
- Use factories for consistent test data
- Test both success and failure scenarios

### API Testing
- Test all CRUD operations
- Verify authentication requirements
- Test input validation (valid/invalid data)
- Check response format consistency

## Coverage Goals

### Target Coverage Metrics
- **Overall Coverage:** > 85%
- **Unit Tests:** > 90% of business logic
- **API Endpoints:** 100% of endpoints tested
- **Authorization:** 100% of policies tested
- **Validation:** 100% of rules tested

### Coverage Areas
- ✅ User Authentication & Authorization
- ✅ Deck Management (CRUD)
- ✅ Card Management (CRUD)
- ✅ Study Sessions
- ✅ API Endpoints
- ✅ Livewire Components
- ✅ Browser Interactions
- ✅ Performance Benchmarks

## Debugging Tests

### Common Issues
1. **Database State:** Use `RefreshDatabase` or run with `--without-databases`
2. **Authentication:** Ensure proper `actingAs()` usage

### Debugging Commands
```bash
# Run with verbose output
php artisan test --verbose

# Run specific test with debugging
php artisan test tests/Feature/Api/AuthApiTest.php --debug

# Run tests in parallel for performance
php artisan test --parallel
```

## Contributing

### Adding New Tests
1. Follow existing naming conventions
2. Use appropriate test category
3. Include both positive and negative test cases
4. Add documentation comments for complex tests
5. Update this README if adding new categories

### Test Organization Guidelines
- **Unit tests** for isolated logic
- **Feature tests** for complete workflows
- **Performance tests** for scalability validation


