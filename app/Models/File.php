<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path',
        'name',
        'file_id',
    ];

    public function url():Attribute
    {
        return Attribute::get(fn() => route('download', $this));
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public  function getAccessArray()
    {
        $accesses = [
            [
                'fullname' => $this->user->first_name . ' ' . $this->user->last_name,
                'email' => $this->user->email,
                'type' => 'author'
            ]
        ];

        $relations = FileAccess::query()->where('file_id', $this->id)->get();

        foreach ($relations as $relation) {
            $accesses [] = [
                'fullname' => $relation->user->first_name . ' ' . $relation->user->last_name,
                'email' => $relation->user->email,
                'type' => 'co-author'
            ];
        }
        return $accesses;
    }
}
