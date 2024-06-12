<?php

namespace Tests\Feature;

use App\Models\Book;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Database\Seeders\BookSeeder;
use Database\Seeders\SearchSeeder;
use Illuminate\Support\Facades\Log;


class BookTest extends TestCase
{
    public function testCreateBookSuccess(): void
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/books', [
            'isbn' => '9781491943533',
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Bevacqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'isbn' => '9781491943533',
                    'title' => 'Practical Modern JavaScript',
                    'subtitle' => 'Dive into ES6 and the Future of JavaScript',
                    'author' => 'Nicolás Bevacqua',
                    'publisher' => 'O Reilly Media',
                    'pages' => 334,
                    'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
                    'website' => 'https://github.com/mjavascript/practical-modern-javascript'
                ]
            ]);
    }
    public function testCreateBookFailed(): void
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/books', [
            'isbn' => '12345',
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Bevacqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "isbn" => [
                        "The isbn field must be at least 10 characters.",
                    ]
                ]
            ]);
    }
    public function testCreateBookUnauthorized(): void
    {
        $this->post('/api/books', [
            'isbn' => '9781491943533',
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Bevacqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testShowBookSuccess(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->get('/api/books/' . $book->id, [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'isbn' => '9781491943533',
                    'title' => 'Practical Modern JavaScript',
                    'subtitle' => 'Dive into ES6 and the Future of JavaScript',
                    'author' => 'Nicolás Bevacqua',
                    'published' => '2017-07-16',
                    'publisher' => 'O Reilly Media',
                    'pages' => 334,
                    'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
                    'website' => 'https://github.com/mjavascript/practical-modern-javascript'
                ]
            ]);
    }
    public function testShowBookNotFound(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $this->get('/api/books/-1', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Book not found"]
                ]
            ]);
    }
    public function testShowBookUnauthorized(): void
    {
        $this->testCreateBookSuccess();
        $id = Book::where('isbn', '9781491943533')->first()->id;
        $this->get('/api/books/' . $id)->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testShowBookForbidden(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->get('/api/books/' . $book->id, [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(403)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateBookSuccess(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->put('/api/books/' . $book->id, [
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Aqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'isbn' => '9781491943533',
                    'title' => 'Practical Modern JavaScript',
                    'subtitle' => 'Dive into ES6 and the Future of JavaScript',
                    'author' => 'Nicolás Aqua',
                    'publisher' => 'O Reilly Media',
                    'pages' => 334,
                    'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
                    'website' => 'https://github.com/mjavascript/practical-modern-javascript'
                ]
            ]);
    }
    public function testUpdateBookNotFound(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $this->put('/api/books/-1', [
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Aqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Book not found"]
                ]
            ]);
    }
    public function testUpdateBookValidationError(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->put('/api/books/' . $book->id, [
            'title' => '',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => '',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field is required.'],
                    'author' => ['The author field is required.']
                ]
            ]);
    }
    public function testUpdateBookUnauthorized(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->put('/api/books/' . $book->id, [
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Aqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ]);
    }
    public function testUpdateBookForbidden(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->put('/api/books/' . $book->id, [
            'title' => 'Practical Modern JavaScript',
            'subtitle' => 'Dive into ES6 and the Future of JavaScript',
            'author' => 'Nicolás Aqua',
            'published' => '2017-07-16',
            'publisher' => 'O Reilly Media',
            'pages' => 334,
            'description' => 'To get the most out of modern JavaScript, you need learn the latest features of its parent specification, ECMAScript 6 (ES6). This book provides a highly practical look at ES6, without getting lost in the specification or its implementation details.',
            'website' => 'https://github.com/mjavascript/practical-modern-javascript'
        ], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(403)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ]);
    }

    public function testDeleteBookSuccess(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->delete('/api/books/' . $book->id, [], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJson([
                'message' => 'Book deleted'
            ]);
    }
    public function testDeleteBookNotFound(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $this->delete('/api/books/-1', [], [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'book' => ['Book not found']
                ]
            ]);
    }
    public function testDeleteBookUnauthorized(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->delete('/api/books/' . $book->id)->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ]);
    }
    public function testDeleteBookForbidden(): void
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();
        $this->delete('/api/books/' . $book->id, [], [
            'Authorization' => 'marryjane_token'
        ])->assertStatus(403)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ]);
    }

    public function testSearchBooksByTitleSuccess(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?title=Book', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchBooksByAuthorSuccess(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?author=Author', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchBooksByPublisherSuccess(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?publisher=Publisher', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchBooksByTitleNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?title=Tidakada', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(0, $response['data']);
        self::assertEquals(0, $response['meta']['total']);
    }
    public function testSearchBooksByAuthorNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?author=Tidakada', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(0, $response['data']);
        self::assertEquals(0, $response['meta']['total']);
    }
    public function testSearchBooksByPublisherNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?publisher=Tidakada', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(0, $response['data']);
        self::assertEquals(0, $response['meta']['total']);
    }
    public function testSearchBooksByPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/books?size=5&page=2', [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(5, $response['data']);
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(5, $response['meta']['per_page']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
