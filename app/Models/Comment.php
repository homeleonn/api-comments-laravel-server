<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TreeBased;

class Comment extends TreeBasedModel
{
    use HasFactory;

    protected $fillable = ['author_name', 'text', 'parent_id'];

    public function toArray()
    {
        return [
            'id'            => $this->id,
            'author_name'   => $this->author_name,
            'text'          => $this->text,
            'parent_id'     => $this->parent_id,
            'created_at'    => $this->created_at->format('d.m.Y в H:i'),
            'updated'       => !is_null($this->updated_at),
            'children'       => $this->children ?? [],
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->timestamps = false;
            $model->created_at = now();
        });
    }
}
