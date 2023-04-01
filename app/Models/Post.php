<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use stdClass;

/**
 * @property string $title
 * @property string $content
 * @property string $media
 * @property string $user_role
 * @property int    $user_id
 * @property int    $course_id
 * @property array  $user
 * @property int    $id
 * @property string $author
 * @property int    $likes
 */
class Post extends Model
{
    use HasFactory,
        AttributesModifier;

    const POST_MEDIA_STORAGE = 'storage/media/post/';

    protected $appends = [
        'likedByMe',
    ];

    protected $fillable = [
        'id',
        'title',
        'content',
        'media',
        'author'
    ];

    /**
     * @var array
     */
    private array $user;

    protected $with = [
        'tags',
    ];

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * added absolute url for media files
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function media(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset(self::POST_MEDIA_STORAGE.$value),
        );
    }

    /**
     * @return bool
     */
    protected function getLikedByMeAttribute(): bool
    {
        return $this
            ->likes()
            ->where('user_id', Auth::user()->getId())
            ->where('user_role', Auth::user()->getRole())
            ->exists();
    }

    /**
     * modify post author to array
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function author(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
        );
    }

    /**
     * @param \stdClass $post
     *
     * @return $this
     */
    public function fromStdClass(stdClass $post): static
    {
        $this->id = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->media = $post->media;
        $this->author = $post->author;
        $this->course_id = $post->course_id;
        $this->likes = $post->likes;
        $this->refresh()->load('tags');

        return $this;
    }
}
