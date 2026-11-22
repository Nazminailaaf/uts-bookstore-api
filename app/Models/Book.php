<?php

namespace App\Models;

use App\Helpers\XmlHelper;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'author_id', 'title', 'category', 'isbn',
        'price', 'published_year',
    ];

    // Relasi kebalikan: book dimiliki oleh satu author
    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function toApi($withAuthor = false)
    {
        $data = [
            'id'             => $this->id,
            'author_id'      => $this->author_id,
            'title'          => $this->title,
            'category'       => $this->category,
            'isbn'           => $this->isbn,
            'price'          => $this->price,
            'published_year' => $this->published_year,
            'created_at'     => XmlHelper::formatTime($this->created_at),
            'updated_at'     => XmlHelper::formatTime($this->updated_at),
        ];

        if ($withAuthor && $this->author) {
            $data['author'] = [
                'id'    => $this->author->id,
                'name'  => $this->author->name,
                'email' => $this->author->email,
            ];
        }

        return $data;
    }
}
