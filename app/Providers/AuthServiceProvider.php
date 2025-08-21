<?php
namespace App\Providers;
use App\Models\Deck;
use App\Models\Study;
use App\Policies\DeckPolicy;
use App\Policies\StudyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Deck::class => DeckPolicy::class,        
        Study::class => StudyPolicy::class,
    ];

    public function boot(): void {}
}