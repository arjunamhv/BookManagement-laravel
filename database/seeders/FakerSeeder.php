<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Book;

class FakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $user = User::where('username', 'arjunamhv')->first();

        for ($i = 0; $i < 25; $i++) {
            Book::create([
                'isbn' => $faker->isbn13,
                'title' => $faker->sentence($nbWords = 3, $variableNbWords = true),
                'subtitle' => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'author' => $faker->name,
                'published' => $faker->date,
                'publisher' => $faker->company,
                'pages' => $faker->numberBetween(100, 1000),
                'description' => $faker->paragraph,
                'website' => $faker->url,
                'user_id' => $user->id,
            ]);
        }
    }
}
