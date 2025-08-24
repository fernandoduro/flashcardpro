<div>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('My Decks') }}
        </h2>
    </x-slot>

    <div class="py-2 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center">
                <div class="mb-4 flex justify-between  gap-4">
                    <x-fab-link dispatch="openCreateModal" text="Add Deck" />
                </div>
            </div>
            <section>
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach ($decks as $deck)
                        <x-deck-card :deck="$deck" />
                    @endforeach
                </div>
            </section>
        </div>
    </div>
        
    <livewire:decks.form/>
    <livewire:components.confirmation-modal />
</div>

