<?php

use App\View\Components\GuestLayout;
use Illuminate\View\Component;

test('guest layout component can be instantiated', function () {
    $component = new GuestLayout();
    expect($component)->toBeInstanceOf(GuestLayout::class);
    expect($component)->toBeInstanceOf(Component::class);
});

test('guest layout component has render method', function () {
    $component = new GuestLayout();
    expect(method_exists($component, 'render'))->toBeTrue();
});

test('guest layout component render method returns correct view name', function () {
    $component = new GuestLayout();
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($view->getName())->toBe('layouts.guest');
});

test('guest layout component is properly namespaced', function () {
    $component = new GuestLayout();
    $reflection = new \ReflectionClass($component);

    expect($reflection->getNamespaceName())->toBe('App\View\Components');
    expect($reflection->getShortName())->toBe('GuestLayout');
});

test('guest layout component can handle view rendering', function () {
    $component = new GuestLayout();

    // Test that the component can be used in Blade templates
    // Note: We can't fully render the view without a slot, but we can verify the view exists
    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($view->getName())->toBe('layouts.guest');

    // Test that the view file exists
    $viewPath = resource_path('views/layouts/guest.blade.php');
    expect(file_exists($viewPath))->toBeTrue();
});

test('guest layout component inherits from base component', function () {
    $component = new GuestLayout();
    $reflection = new \ReflectionClass($component);

    expect($reflection->isSubclassOf(Component::class))->toBeTrue();
});

test('guest layout component has expected public methods', function () {
    $component = new GuestLayout();
    $reflection = new \ReflectionClass($component);
    $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

    $publicMethodNames = array_map(fn($method) => $method->getName(), $methods);

    // Should have render method and possibly some inherited methods from Component
    expect($publicMethodNames)->toContain('render');

    // Check that our custom GuestLayout class defines the render method
    $ownMethods = [];
    foreach ($methods as $method) {
        if ($method->getDeclaringClass()->getName() === GuestLayout::class) {
            $ownMethods[] = $method;
        }
    }

    expect(count($ownMethods))->toBe(1);
    expect($ownMethods[0]->getName())->toBe('render');
});
