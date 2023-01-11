<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comment extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'content',
        'rate'
    ];

    public function children() : HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function parent() : HasOne
    {
        return $this->hasOne(Comment::class, 'parent_id', 'id');
    }

    public function question() : BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
