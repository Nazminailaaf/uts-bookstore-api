<?php

namespace App\Http\Controllers;

use App\Helpers\XmlHelper;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    private function rules($id = null)
    {
        // saat update, ISBN boleh sama dengan milik book itu sendiri
        $isbnUnique = 'unique:books,isbn' . ($id ? ',' . $id : '');

        return [
            'author_id'      => 'required|exists:authors,id',
            'title'          => 'required|min:3|max:150',
            'category'       => 'required|max:50',
            'isbn'           => 'required|min:10|max:17|' . $isbnUnique,
            'price'          => 'required|numeric|min:0',
            'published_year' => 'required|integer|min:1000|max:2100',
        ];
    }

    // GET /books             -> semua book
    // GET /books?author_id=1 -> book milik author tertentu (filter)
    public function index(Request $request)
    {
        $query = Book::orderBy('id', 'ASC');

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->input('author_id'));
        }

        $books = $query->get()->map(function ($book) {
            return $book->toApi();
        })->all();

        return XmlHelper::response(['books' => ['book' => $books]]);
    }

    // GET /books/{id}
    public function show($id)
    {
        $book = Book::with('author')->find($id);

        if (!$book) {
            abort(404);
        }

        return XmlHelper::response(['book' => $book->toApi(true)]);
    }

    // POST /books   (Basic Auth)
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules());
        if ($validator->fails()) {
            return XmlHelper::response(
                null, 400, 'VALIDATION_ERROR', $validator->errors()->toArray()
            );
        }

        $book = Book::create($input);

        return XmlHelper::response(['book' => $book->toApi()], 201, 'BOOK_CREATED');
    }

    // PUT /books/{id}   (Basic Auth)
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $book = Book::find($id);
        if (!$book) {
            abort(404);
        }

        $validator = Validator::make($input, $this->rules($id));
        if ($validator->fails()) {
            return XmlHelper::response(
                null, 400, 'VALIDATION_ERROR', $validator->errors()->toArray()
            );
        }

        $book->fill($input);
        $book->save();

        return XmlHelper::response(['book' => $book->toApi()], 200, 'BOOK_UPDATED');
    }

    // DELETE /books/{id}   (Basic Auth)
    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) {
            abort(404);
        }

        $book->delete();

        return XmlHelper::response(null, 200, 'BOOK_DELETED');
    }
}
