<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;
    use AttributesModifier;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    /**
     * @return BelongsToMany
     */
    public function posts() : BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
