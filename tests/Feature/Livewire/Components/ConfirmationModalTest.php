<?php

use App\Livewire\Components\ConfirmationModal;
use Livewire\Livewire;

test('confirmation modal can be opened with correct data', function () {
    Livewire::test(ConfirmationModal::class)
        ->call('open', 'Delete Item', 'Are you sure you want to delete this item?', 'deleteItem', 123)
        ->assertSet('title', 'Delete Item')
        ->assertSet('message', 'Are you sure you want to delete this item?')
        ->assertSet('confirmAction', 'deleteItem')
        ->assertSet('itemId', 123)
        ->assertDispatched('open-modal', 'confirmation-modal');
});

test('confirmation modal can be confirmed', function () {
    Livewire::test(ConfirmationModal::class)
        ->set('confirmAction', 'deleteItem')
        ->set('itemId', 456)
        ->call('confirm')
        ->assertDispatched('deleteItem', 456)
        ->assertDispatched('close-modal', 'confirmation-modal');
});

test('confirmation modal handles empty data gracefully', function () {
    Livewire::test(ConfirmationModal::class)
        ->call('open', '', '', '', 0)
        ->assertSet('title', '')
        ->assertSet('message', '')
        ->assertSet('confirmAction', '')
        ->assertSet('itemId', 0);
});

test('confirmation modal handles long text content', function () {
    $longTitle = str_repeat('Long Title ', 10);
    $longMessage = str_repeat('Long message content. ', 20);

    Livewire::test(ConfirmationModal::class)
        ->call('open', $longTitle, $longMessage, 'testAction', 789)
        ->assertSet('title', $longTitle)
        ->assertSet('message', $longMessage);
});

test('confirmation modal handles special characters in content', function () {
    $titleWithSpecialChars = 'Delete Item: ¿Estás seguro?';
    $messageWithSpecialChars = 'This action cannot be undone. Are you sure? (确认/確認)';

    Livewire::test(ConfirmationModal::class)
        ->call('open', $titleWithSpecialChars, $messageWithSpecialChars, 'confirmDelete', 101)
        ->assertSet('title', $titleWithSpecialChars)
        ->assertSet('message', $messageWithSpecialChars);
});

test('confirmation modal resets state properly', function () {
    $component = Livewire::test(ConfirmationModal::class);

    // Set initial values
    $component->set('title', 'Test Title')
        ->set('message', 'Test Message')
        ->set('confirmAction', 'testAction')
        ->set('itemId', 999);

    // Open with new data
    $component->call('open', 'New Title', 'New Message', 'newAction', 111);

    // Verify new values
    $component->assertSet('title', 'New Title')
        ->assertSet('message', 'New Message')
        ->assertSet('confirmAction', 'newAction')
        ->assertSet('itemId', 111);
});

test('confirmation modal handles numeric item IDs', function () {
    Livewire::test(ConfirmationModal::class)
        ->call('open', 'Test', 'Message', 'action', 0)
        ->assertSet('itemId', 0);

    Livewire::test(ConfirmationModal::class)
        ->call('open', 'Test', 'Message', 'action', -1)
        ->assertSet('itemId', -1);

    Livewire::test(ConfirmationModal::class)
        ->call('open', 'Test', 'Message', 'action', 999999)
        ->assertSet('itemId', 999999);
});

test('confirmation modal handles string item IDs', function () {
    Livewire::test(ConfirmationModal::class)
        ->call('open', 'Test', 'Message', 'action', 'string-id')
        ->assertSet('itemId', 'string-id');
});

test('confirmation modal confirm method dispatches correct events', function () {
    $component = Livewire::test(ConfirmationModal::class);

    // Test with various data types
    $component->set('confirmAction', 'deleteUser')
        ->set('itemId', 'user-123')
        ->call('confirm')
        ->assertDispatched('deleteUser', 'user-123')
        ->assertDispatched('close-modal', 'confirmation-modal');
});

test('confirmation modal handles empty confirm action', function () {
    Livewire::test(ConfirmationModal::class)
        ->set('confirmAction', '')
        ->set('itemId', 123)
        ->call('confirm')
        ->assertDispatched('', 123)
        ->assertDispatched('close-modal', 'confirmation-modal');
});

test('confirmation modal handles zero item ID', function () {
    Livewire::test(ConfirmationModal::class)
        ->set('confirmAction', 'testAction')
        ->set('itemId', 0)
        ->call('confirm')
        ->assertDispatched('testAction', 0)
        ->assertDispatched('close-modal', 'confirmation-modal');
});

test('confirmation modal renders without errors', function () {
    $component = Livewire::test(ConfirmationModal::class);

    // Test rendering with default empty state
    $component->assertOk();

    // Test rendering with populated state
    $component->set('title', 'Test Title')
        ->set('message', 'Test Message')
        ->set('confirmAction', 'testAction')
        ->set('itemId', 123)
        ->assertOk();
});
