<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
     public function run(): void
    {
        // Create 2 specific users
        
        // Create decks and cards
        if (!User::where('email', 'contato@fernandoduro.com.br')->exists()) {
            $user1 = User::factory()->create([
                'name' => 'Fernando Duro',
                'email' => 'contato@fernandoduro.com.br',
                'password' => '$2y$12$b4z3sshFNfnoeZa5V4EIVOZuRDi6Sj1GpR.AsFL3HdskPohPEtv9K',
            ]);
            Deck::factory(3)
                ->for($user1)
                ->has(Card::factory()->count(10)->for($user1))
                ->create();
        }

        $this->command->info('Seeding Cyberpunk Lore deck...');

        // 1. Find or create the user who will own this deck
        $user = User::firstWhere('email', 'contato@fernandoduro.com.br');
        if (!$user) {
            $this->command->error('User "contato@fernandoduro.com.br" not found. Please seed the user first.');
            return;
        }

        // 2. Find or create the "Cyberpunk's Lore" deck for this user
        $deck = Deck::firstOrCreate(
            ['name' => 'Cyberpunk\'s Lore', 'user_id' => $user->id],
            ['public' => true] // Additional attributes if it needs to be created
        );

        // 3. Define the array of cards to be seeded
        $cyberpunkCards = [
            ['question' => 'What is the common slang term for a corporate employee, typically used with derision by those on the street?', 'answer' => 'Corpo'],
            ['question' => 'What gang, primarily based in Pacifica, are master Netrunners seeking contact with rogue AIs beyond the Blackwall?', 'answer' => 'The Voodoo Boys'],
            ['question' => 'What is the professional title for a surgeon who specializes in installing, upgrading, and maintaining cyberware?', 'answer' => 'Ripperdoc'],
            ['question' => 'What American-based arms manufacturing megacorp is Arasaka\'s main rival, playing a key role in the 4th Corporate War?', 'answer' => 'Militech'],
            ['question' => 'What is the term for the mental illness caused by an overwhelming amount of cybernetic implants, leading to a violent loss of humanity?', 'answer' => 'Cyberpsychosis'],
            ['question' => 'What is the name of the technology that allows a user to digitally experience another person\'s memories and sensory inputs?', 'answer' => 'Braindance (or BD)'],
            ['question' => 'Which combat-obsessed gang, known for extreme cybernetic modification to the point of psychosis, has a stronghold in the All Foods plant?', 'answer' => 'Maelstrom'],
            ['question' => 'In Night City\'s underworld, what is the title for a middleman who brokers deals, finds jobs, and connects mercenaries with clients?', 'answer' => 'Fixer'],
            ['question' => 'What is the name of the legendary fixer bar in Night City where major league edgerunners make deals, run by Rogue Amendiares?', 'answer' => 'The Afterlife'],
            ['question' => 'Who was the legendary netrunner responsible for unleashing the R.A.B.I.D.S. data-plague, which led to the collapse of the Old Net?', 'answer' => 'Rache Bartmoss'],
            ['question' => 'Often called the "best solo of the 2020s," who was Militech\'s top operative and Johnny Silverhand\'s direct rival?', 'answer' => 'Morgan Blackhand'],
            ['question' => 'What is the name for the arm cyberware that deploys a pair of sharp, foldable blades for melee combat?', 'answer' => 'Mantis Blades'],
            ['question' => 'What is the name for the desolate, arid region surrounding Night City, populated by various Nomad clans?', 'answer' => 'The Badlands'],
            ['question' => 'Named in memory of a murdered strip club owner, which gang is dedicated to protecting sex workers from violence and exploitation?', 'answer' => 'The Mox'],
            ['question' => 'Which corporation is the leading manufacturer of ocular cyberware, with their "Kiroshi Optics" being a standard for many in Night City?', 'answer' => 'Kiroshi'],
            ['question' => 'What is the name of V\'s loyal partner and best friend at the beginning of Cyberpunk 2077?', 'answer' => 'Jackie Welles'],
            ['question' => 'What is the common Night City slang word for an idiot or a fool?', 'answer' => 'Gonk'],
            ['question' => 'What is the name of the premier 24/7 news network in Night City, often shortened to N54?', 'answer' => 'Network News 54'],
            ['question' => 'Who is the fiercely loyal, former bodyguard to Saburo Arasaka who seeks V\'s help to expose his master\'s murderer?', 'answer' => 'Goro Takemura'],
            ['question' => 'What is the title for a heavily augmented mercenary, bodyguard, or assassin, considered the "street samurai" of the Cyberpunk world?', 'answer' => 'Solo'],
        ];

        // 4. Loop through the cards, create them if they don't exist, and attach them to the deck
        foreach ($cyberpunkCards as $cardData) {
            $card = Card::firstOrCreate(
                ['question' => $cardData['question'], 'user_id' => $user->id],
                ['answer' => $cardData['answer']]
            );

            // Attach the card to the deck if it's not already attached
            $deck->cards()->syncWithoutDetaching($card->id);
        }

        $this->command->info('Cyberpunk Lore deck seeded successfully!');
    }
}
