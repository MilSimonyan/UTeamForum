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
 * @property        $id
 */
class Post extends Model
{
    use HasFactory,
        AttributesModifier;

    const POST_MEDIA_STORAGE = 'app/media/post/';

    protected $appends = [
        'likedByMe',
        'user'
    ];

    protected $fillable = [
        'id',
        'title',
        'content',
        'media',
    ];

    /**
     * @var array
     */
    private array $user;

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
        return Attribute::make(
            get: fn($value) => asset(self::POST_MEDIA_STORAGE.$value),
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

    /**
     * @return array
     */
    protected function getUserAttribute(): array
    {
        if (isset($this->user))
        {
            return $this->user;
        }

        return [
            'id'        => $this->user_id,
            'firstName' => auth()->user()->getFirstName(),
            'lastName'  => auth()->user()->getLastName(),
            'role'      => $this->user_role
            //            'thumbnail' => auth()->user()->getThumbnail() TODO after added from user
        ];
    }

    /**
     * @param array $user
     */
    public function setUser(array $user): void
    {
        $this->user = $user;
    }

    /**
     * @param \stdClass $post
     *
     * @return $this
     */
    public function fromStdClass(stdClass $post) : static
    {
        $this->id = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->media = $post->media;
        $this->setUser([
            'id'        => $post->user_id,
            'firstName' => auth()->user()->getFirstName(),
            'lastName'  => auth()->user()->getLastName(),
            'role'      => $post->user_role
//            'thumbnail' => auth()->user()->getThumbnail() TODO after added from user
        ]);
        $this->course_id = $post->course_id;
        $this->refresh()->load('tags');

        return $this;
    }
}
