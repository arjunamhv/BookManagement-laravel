<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;


class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'janedoe')->first();
        for ($i = 0; $i < 20; $i++) {
            do {
                $isbn = mt_rand(1000000000000, 9999999999999);
            } while (Book::where('isbn', $isbn)->exists());
            Book::create([
                'isbn' => $isbn,
                'user_id' => $user->id,
                'title' => 'Book ' . $i,
                'author' => 'Author ' . $i,
                'publisher' => 'Publisher ' . $i,
            ]);
        }
    }
}
