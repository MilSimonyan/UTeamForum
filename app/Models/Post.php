<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $title
 * @property string $content
 * @property string $media
 * @property string $user_role
 * @property int $user_id
 * @property int $course_id
 */
class Post extends Model
{
    use HasFactory,
        AttributesModifier;

    const POST_MEDIA_STORAGE = 'app/media/post/';

    protected $fillable = [
        'title',
        'content',
        'media',
    ];

    /**
     * @return HasMany
     */
    public function rates() : HasMany
    {
        return $this->hasMany(PostRate::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    protected function media() : Attribute
    {
        $path = storage_path(self::POST_MEDIA_STORAGE);
        return Attribute::make(
            get: fn($value) => $path . $value,
        );
    }
}
