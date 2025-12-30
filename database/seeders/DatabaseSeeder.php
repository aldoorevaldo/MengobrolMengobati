<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $groups = [
            ['title'=>'Anxiety Support','description'=>'Tempat berbagi pengalaman dan coping untuk kecemasan.'],
            ['title'=>'Self-Love','description'=>'Diskusi dan tips membangun self-worth.'],
            ['title'=>'Overthinking Corner','description'=>'Ceritakan pengalaman mengatasi overthinking.'],
        ];

        foreach ($groups as $g) {
            TherapyGroup::firstOrCreate(
                ['slug'=>Str::slug($g['title'])],
                ['title'=>$g['title'],'description'=>$g['description']]
            );
        }
    }
}
