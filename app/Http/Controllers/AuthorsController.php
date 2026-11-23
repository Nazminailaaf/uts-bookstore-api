<?php

namespace App\Http\Controllers;

use App\Helpers\XmlHelper;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorsController extends Controller
{
    private function rules($id = null)
    {
        // saat update, email boleh sama dengan milik author itu sendiri
        $emailUnique = 'unique:authors,email' . ($id ? ',' . $id : '');

        return [
            'name'        => 'required|min:3|max:100',
            'email'       => 'required|email|max:100|' . $emailUnique,
            'phone'       => 'required|min:8|max:20',
            'address'     => 'required|min:5|max:255',
            'gender'      => 'required|in:male,female',
            'birth_date'  => 'required|date_format:Y-m-d',
            'nationality' => 'required|max:50',
            'bio'         => 'required|min:10',
            'website'     => 'nullable|url|max:150',
        ];
    }

    // GET /authors
    public function index()
    {
        $authors = Author::orderBy('id', 'ASC')->get()
            ->map(function ($author) {
                return $author->toApi();
            })->all();

        return XmlHelper::response(['authors' => ['author' => $authors]]);
    }

    // GET /authors/{id}
    public function show($id)
    {
        $author = Author::find($id);

        if (!$author) {
            abort(404);
        }

        return XmlHelper::response(['author' => $author->toApi(true)]);
    }

    // POST /authors   (Basic Auth)
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules());
        if ($validator->fails()) {
            return XmlHelper::response(
                null, 400, 'VALIDATION_ERROR', $validator->errors()->toArray()
            );
        }

        $author = Author::create($input);

        return XmlHelper::response(['author' => $author->toApi()], 201, 'AUTHOR_CREATED');
    }

    // PUT /authors/{id}   (Basic Auth)
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $author = Author::find($id);
        if (!$author) {
            abort(404);
        }

        $validator = Validator::make($input, $this->rules($id));
        if ($validator->fails()) {
            return XmlHelper::response(
                null, 400, 'VALIDATION_ERROR', $validator->errors()->toArray()
            );
        }

        $author->fill($input);
        $author->save();

        return XmlHelper::response(['author' => $author->toApi()], 200, 'AUTHOR_UPDATED');
    }

    // DELETE /authors/{id}   (Basic Auth)
    public function destroy($id)
    {
        $author = Author::find($id);

        if (!$author) {
            abort(404);
        }

        $author->delete(); // books milik author ikut terhapus (ON DELETE CASCADE)

        return XmlHelper::response(null, 200, 'AUTHOR_DELETED');
    }
}