<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     * get /books
     */
    public function index(Request $request): BookCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $books = Book::where('user_id', $user->id);

        $books = $books->where(function (Builder $builder) use ($request) {
            $title = $request->input('title');
            if ($title) {
                $builder->where(function (Builder $builder) use ($title) {
                    $builder->orWhere('title', 'like', '%' . $title . '%');
                });
            }

            $author = $request->input('author');
            if ($author) {
                $builder->where(function (Builder $builder) use ($author) {
                    $builder->orWhere('author', 'like', '%' . $author . '%');
                });
            }

            $publisher = $request->input('publisher');
            if ($publisher) {
                $builder->where(function (Builder $builder) use ($publisher) {
                    $builder->orWhere('publisher', 'like', '%' . $publisher . '%');
                });
            }
        });

        $books = $books->paginate(perPage: $size, page: $page);

        return new BookCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     * post /books
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $book = new Book($data);
        $book->user_id = $user->id;
        $book->save();

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     * get /books/{book}
     */
    public function show(Int $id): BookResource
    {
        $user = Auth::user();
        $book = Book::where('id', $id)->first();
        if (!$book) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Book not found']
                ]
            ])->setStatusCode(404));
        }
        if ($book->user_id !== $user->id) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ])->setStatusCode(403));
        }
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     * put /books/{book}
     */
    public function update(UpdateBookRequest $request, int $id): BookResource
    {
        $user = Auth::user();

        $book = Book::where('id', $id)->first();
        if (!$book) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Book not found']
                ]
            ])->setStatusCode(404));
        }
        if ($book->user_id !== $user->id) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ])->setStatusCode(403));
        }

        $data = $request->validated();
        $book->title = $data['title'];
        $book->subtitle = $data['subtitle'];
        $book->author = $data['author'];
        $book->published = $data['published'];
        $book->publisher = $data['publisher'];
        $book->pages = $data['pages'];
        $book->description = $data['description'];
        $book->website = $data['website'];

        $book->save();

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     * delete /books/{book}
     */
    public function destroy(int $id)
    {
        $user = Auth::user();

        $book = Book::where('id', $id)->first();
        if (!$book) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'book' => ['Book not found']
                ]
            ])->setStatusCode(404));
        }
        if ($book->user_id !== $user->id) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Unauthorized']
                ]
            ])->setStatusCode(403));
        }

        $book->delete();
        return response()->json([
            'message' => 'Book deleted'
        ], 200);
    }
}
