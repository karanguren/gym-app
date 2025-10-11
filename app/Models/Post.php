<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'user_id',
    ];

    /**
     * Una publicación pertenece a un usuario (autor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}