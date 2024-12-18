<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    public function product():belongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Un comentario puede tener un comentario padre (si es una respuesta)
    public function parent():belongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Un comentario puede tener varios comentarios hijos (respuestas)
    public function replies():hasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Un comentario pertenece a un usuario
    public function user():belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
