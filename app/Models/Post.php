<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $title
 * @property string $content
 * @property string $media
 * @property string $user_role
 * @property int    $user_id
 * @property int    $course_id
 */
class Post extends Model
{
    use HasFactory,
        AttributesModifier;

    const POST_MEDIA_STORAGE = 'app/media/post/';

    protected $appends = [
        'likedByMe'
    ];

    protected $fillable = [
        'title',
        'content',
        'media',
    ];

    protected $withCount = [
        'likes'
    ];

    protected $with = [
        'tags',
    ];

    /**
     * @return HasMany
     */
    public function likes() : HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * added absolute url for media files
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function media() : Attribute
    {
        $path = storage_path(self::POST_MEDIA_STORAGE);
        return Attribute::make(
            get: fn($value) => $path.$value,
        );
    }

    /**
     * @return bool
     */
    protected function getLikedByMeAttribute() : bool
    {
        return $this
            ->likes()
            ->where('user_id', Auth::user()->getId())
            ->where('user_role', Auth::user()->getRole())
            ->exists();
    }
}
