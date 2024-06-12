<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'janedoe')->first();
        Book::create([
            'user_id' => $user->id,
            'isbn' => '9781491943533',
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Bevacqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ]);
    }
}
