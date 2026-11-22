<?php

namespace App\Models;

use App\Helpers\XmlHelper;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'authors';

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'gender',
        'birth_date', 'nationality', 'bio', 'website',
    ];

    // Relasi: satu author memiliki banyak book (has_many)
    public function books()
    {
        return $this->hasMany(Book::class, 'author_id');
    }

    // Bentuk array yang akan diubah menjadi XML
    public function toApi($withBooks = false)
    {
        $data = [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'address'     => $this->address,
            'gender'      => $this->gender,
            'birth_date'  => $this->birth_date,
            'nationality' => $this->nationality,
            'bio'         => $this->bio,
            'website'     => $this->website,
            'created_at'  => XmlHelper::formatTime($this->created_at),
            'updated_at'  => XmlHelper::formatTime($this->updated_at),
        ];

        if ($withBooks) {
            $data['books'] = [
                'book' => $this->books->map(function ($book) {
                    return $book->toApi();
                })->all(),
            ];
        }

        return $data;
    }
}
