<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\StudyResult;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 2 specific users

        // Create decks and cards
        if (! User::where('email', 'admin@example.com')->exists()) {
            $user1 = User::factory()->create([
                'name' => 'Fernando Duro',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);

            Deck::factory(3)
                ->for($user1)
                ->create()->each(function ($deck) {
                    // For each created deck, create 10 cards that belong to it
                    Card::factory(10)->create([
                        'deck_id' => $deck->id,
                        'user_id' => $deck->user_id,
                    ]);
                });

        }

        $this->command->info('Seeding Cyberpunk Lore deck...');

        $user = User::firstWhere('email', 'admin@example.com');
        if (! $user) {
            $this->command->error('User "admin@example.com" not found. Please seed the user first.');

            return;
        }

        $sourcePath = public_path('images/deck_seeds/cyberpunk.webp'); // Path to the image in public
        $destinationPath = storage_path('app/public/seeded_images/cyberpunk.webp'); // Path in storage

        // Create the directory if it doesn't exist
        if (! File::isDirectory(dirname($destinationPath))) {
            File::makeDirectory(dirname($destinationPath), 0755, true, true);
        }

        File::copy($sourcePath, $destinationPath);

        $deck = Deck::firstOrCreate(
            [
                'name' => 'Cyberpunk\'s Lore',
                'user_id' => $user->id,
            ],
            [
                'public' => true,
                'is_pinned' => true,
                'cover_image_path' => 'seeded_images/cyberpunk.webp',
            ]
        );

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

        foreach ($cyberpunkCards as $cardData) {
            try {
                $deck->cards()->firstOrCreate(
                    [
                        'question' => $cardData['question'],
                        'user_id' => $user->id,
                    ],
                    [
                        'answer' => $cardData['answer'],
                    ]
                );
            } catch (\Exception $e) {
                $this->command->error("Failed to create Cyberpunk card: {$cardData['question']} - ".$e->getMessage());
            }
        }

        $this->command->info('Cyberpunk Lore deck seeded successfully!');

        $this->command->info('Seeding Fallout Lore deck...');

        $user = User::firstWhere('email', 'admin@example.com');
        if (! $user) {
            $this->command->error('User "admin@example.com" not found. Please seed the user first.');

            return;
        }

        $sourcePath = public_path('images/deck_seeds/fallout.jpeg'); // Path to the image in public
        $destinationPath = storage_path('app/public/seeded_images/fallout.jpeg'); // Path in storage

        // Create the directory if it doesn't exist
        if (! File::isDirectory(dirname($destinationPath))) {
            File::makeDirectory(dirname($destinationPath), 0755, true, true);
        }

        File::copy($sourcePath, $destinationPath);

        $deck = Deck::firstOrCreate(
            [
                'name' => 'Fallout\'s Lore',
                'user_id' => $user->id,
            ],
            [
                'public' => true,
                'is_pinned' => true,
                'cover_image_path' => 'seeded_images/fallout.jpeg',
            ]
        );

        $falloutCards = [
            ['question' => 'What remnant of the pre-war U.S. government claims to be its true successor and served as the main antagonist in both Fallout 2 and Fallout 3?', 'answer' => 'The Enclave.'],
            ['question' => 'In Fallout: New Vegas, what is the name of the platinum casino chip the Courier was hired to deliver?', 'answer' => 'The Platinum Chip.'],
            ['question' => 'What is the name of the super mutant leader of the Unity, who served as the primary antagonist of the original Fallout?', 'answer' => 'The Master.'],
            ['question' => 'What is the name of the town built around an unexploded atomic bomb in the Capital Wasteland?', 'answer' => 'Megaton.'],
            ['question' => 'What model of personal information processor, worn on the wrist, is standard issue for Vault Dwellers?', 'answer' => 'The Pip-Boy.'],
            ['question' => 'The protagonist of Fallout 2, known as the "Chosen One," is a direct descendant of which earlier protagonist?', 'answer' => 'The Vault Dweller (from Fallout 1).'],
            ['question' => 'What is the name of the miraculous pre-war terraforming device that can create a thriving settlement from barren wasteland?', 'answer' => 'The G.E.C.K. (Garden of Eden Creation Kit).'],
            ['question' => 'What faction in the Commonwealth is dedicated to freeing Synths and operates using a secret network of safehouses and codenames?', 'answer' => 'The Railroad.'],
            ['question' => 'What is the name of the friendly Securitron who pulls the Courier from their shallow grave at the beginning of Fallout: New Vegas?', 'answer' => 'Victor.'],
            ['question' => 'What is the general term for humans who have been heavily irradiated but not killed, resulting in mutated bodies and an extremely long lifespan?', 'answer' => 'Ghouls.'],
            ['question' => 'What model of power armor is most iconically associated with the Brotherhood of Steel and appears on the cover of Fallout 4?', 'answer' => 'T-60 Power Armor.'],
            ['question' => 'What major pre-war conflict, fought over the last remaining resources, directly preceded the Great War?', 'answer' => 'The Sino-American War.'],
            ['question' => 'Who is the enigmatic, pre-war ruler of the New Vegas Strip who has kept himself alive for over 200 years using advanced technology?', 'answer' => 'Mr. House (Robert House).'],
            ['question' => 'What highly addictive chem, known for its ability to slow time for the user, was invented in New Reno by a young genius named Myron?', 'answer' => 'Jet.'],
            ['question' => 'What is the name of the volunteer militia in the Commonwealth dedicated to protecting settlements, famously led by Preston Garvey?', 'answer' => 'The Minutemen.'],
            ['question' => 'What was the first successful post-war settlement, founded by inhabitants of Vault 15, that would later become the capital of the NCR?', 'answer' => 'Shady Sands.'],
            ['question' => 'The Sole Survivor emerges from Vault 111 with the primary goal of finding who?', 'answer' => 'Their kidnapped son, Shaun.'],
            ['question' => 'Caesar\'s Legion models its structure, culture, and military tactics after what ancient empire?', 'answer' => 'The Roman Empire.'],
            ['question' => 'What robotics corporation, founded by Robert House, is responsible for creating the Pip-Boy, Securitrons, and the operating system for most pre-war terminals?', 'answer' => 'RobCo Industries.'],
            ['question' => 'In Fallout 3, the Lone Wanderer\'s father, James, leaves Vault 101 to restart what scientific endeavor?', 'answer' => 'Project Purity.'],
        ];

        foreach ($falloutCards as $cardData) {
            try {
                $deck->cards()->firstOrCreate(
                    [
                        'question' => $cardData['question'],
                        'user_id' => $user->id,
                    ],
                    [
                        'answer' => $cardData['answer'],
                    ]
                );
            } catch (\Exception $e) {
                $this->command->error("Failed to create Fallout card: {$cardData['question']} - ".$e->getMessage());
            }
        }

        $this->command->info('Fallout Lore deck seeded successfully!');

        $this->command->info('Seeding The Elder Scroll\'s Lore deck...');

        $user = User::firstWhere('email', 'admin@example.com');
        if (! $user) {
            $this->command->error('User "admin@example.com" not found. Please seed the user first.');

            return;
        }

        $sourcePath = public_path('images/deck_seeds/skyrim.png'); // Path to the image in public
        $destinationPath = storage_path('app/public/seeded_images/skyrim.png'); // Path in storage

        // Create the directory if it doesn't exist
        if (! File::isDirectory(dirname($destinationPath))) {
            File::makeDirectory(dirname($destinationPath), 0755, true, true);
        }

        File::copy($sourcePath, $destinationPath);

        $deck = Deck::firstOrCreate(
            [
                'name' => 'The Elder Scrolls\' Lore',
                'user_id' => $user->id,
            ],
            [
                'public' => true,
                'is_pinned' => true,
                'cover_image_path' => 'seeded_images/skyrim.png',
            ]
        );

        $skyrimCards = [
            ['question' => 'What is the name of the Nordic afterlife, the hall of valor where great warriors feast and await the final battle?', 'answer' => 'Sovngarde.'],
            ['question' => 'Which Daedric Prince is the "Father of Manbeasts" and patron of hunters, presiding over the Great Hunt?', 'answer' => 'Hircine.'],
            ['question' => 'What is the proper name for the race of elves who lived in Skyrim before the Nords, now known as the monstrous Falmer?', 'answer' => 'The Snow Elves.'],
            ['question' => 'What is the name of the Khajiit homeland, a land of deserts and jungles whose people are intrinsically tied to the phases of the moons?', 'answer' => 'Elsweyr.'],
            ['question' => 'Which living god of the Dunmer Tribunal is known as the "Poet-Warrior" and is famous for his dual-colored skin?', 'answer' => 'Vivec.'],
            ['question' => 'What is the name of the high-elven political faction that rules the Aldmeri Dominion and believes in the supremacy of mer over man?', 'answer' => 'The Thalmor.'],
            ['question' => 'What is the name of the ancient order sworn to protect and serve the Dragonborn, acting as the emperor\'s eyes and ears?', 'answer' => 'The Blades.'],
            ['question' => 'Which Daedric Prince rules the realm of Apocrypha and hoards all forbidden and forgotten knowledge?', 'answer' => 'Hermaeus Mora.'],
            ['question' => 'What is the name of the oldest known structure in Tamriel, a tower where the Aedra first convened to decide the fate of creation?', 'answer' => 'The Adamantine Tower (or Direnni Tower).'],
            ['question' => 'What sacred Imperial artifact, worn by Dragonborn emperors, created a barrier between Mundus and the realms of Oblivion?', 'answer' => 'The Amulet of Kings.'],
            ['question' => 'What are the magical crystals, both black and white, that are used to capture the souls of living beings?', 'answer' => 'Soul Gems.'],
            ['question' => 'What was the "Dragon War"?', 'answer' => 'An ancient conflict where humanity, aided by a few dragons like Paarthurnax, overthrew their dragon overlords.'],
            ['question' => 'Who was Pelinal Whitestrake?', 'answer' => 'A legendary, star-made hero who led humanity\'s slave rebellion against their Ayleid (Wild Elf) masters.'],
            ['question' => 'In the Dunmer faith, Azura, Boethiah, and Mephala are known collectively by what title?', 'answer' => 'The "Good Daedra" or The Reclamations.'],
            ['question' => 'The physical form of a Khajiit is determined by the lunar phases at their birth. What are the names of Tamriel\'s two moons?', 'answer' => 'Masser and Secunda.'],
            ['question' => 'What is the name of the Psijic Order\'s hidden island headquarters, which vanished from Tamriel for thousands of years?', 'answer' => 'Artaeum.'],
            ['question' => 'Which Daedric Prince is known as the "Prince of Pacts" and the "King of Rape," and was the main antagonist of The Elder Scrolls Online?', 'answer' => 'Molag Bal.'],
            ['question' => 'What major conflict between the Third Empire and the Aldmeri Dominion ended with the signing of the controversial White-Gold Concordat?', 'answer' => 'The Great War.'],
            ['question' => 'What is the name of the Orc homeland, a city-state that has been built and destroyed multiple times throughout history?', 'answer' => 'Orsinium.'],
            ['question' => 'What is the esoteric, metaphysical term for a state of being where one realizes they are a figure within a dream (the "Godhead") and can impose their will upon it?', 'answer' => 'CHIM.'],
        ];

        foreach ($skyrimCards as $cardData) {
            try {
                $deck->cards()->firstOrCreate(
                    [
                        'question' => $cardData['question'],
                        'user_id' => $user->id,
                    ],
                    [
                        'answer' => $cardData['answer'],
                    ]
                );
            } catch (\Exception $e) {
                $this->command->error("Failed to create Skyrim card: {$cardData['question']} - ".$e->getMessage());
            }
        }

        $this->command->info('The Elder Scrolls\' Lore deck seeded successfully!');

        $this->command->info('Creating sample study sessions...');

        $user = User::firstWhere('email', 'admin@example.com');

        // Create completed study sessions with realistic data
        $decks = Deck::where('user_id', $user->id)->get();
        foreach ($decks as $deck) {
            try {
                // Create 3-5 completed studies per deck
                $studyCount = rand(3, 5);
                for ($i = 0; $i < $studyCount; $i++) {
                    $study = Study::factory()->create([
                        'user_id' => $user->id,
                        'deck_id' => $deck->id,
                        'completed_at' => now()->subDays(rand(1, 30)),
                    ]);

                    // Create study results for each card in the deck
                    $cards = $deck->cards()->get();
                    foreach ($cards as $card) {
                        StudyResult::factory()->create([
                            'study_id' => $study->id,
                            'card_id' => $card->id,
                            'is_correct' => rand(1, 10) > 3, // 70% correct rate
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $this->command->error("Failed to create study sessions for deck '{$deck->name}': ".$e->getMessage());
            }
        }

        // 2. Add edge case decks
        $this->command->info('Creating edge case decks...');

        // Empty deck
        Deck::factory()->create([
            'user_id' => $user->id,
            'name' => 'Empty Deck',
            'public' => false,
        ]);

        // Deck with only 1 card
        $singleCardDeck = Deck::factory()->create([
            'user_id' => $user->id,
            'name' => 'Single Card Deck',
            'public' => false,
        ]);
        Card::factory()->create([
            'user_id' => $user->id,
            'deck_id' => $singleCardDeck->id,
            'question' => 'What is the capital of France?',
            'answer' => 'Paris',
        ]);

        // 3. Add a second user with public decks for API demo
        $user2 = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        $publicDeck = Deck::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Public Demo Deck',
            'public' => true,
        ]);

        Card::factory(5)->create([
            'user_id' => $user2->id,
            'deck_id' => $publicDeck->id,
        ]);
    }
}
