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
class Question extends Model
{
    use HasFactory,
        AttributesModifier;

    const QUESTION_MEDIA_STORAGE = 'app/media/question/';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'content',
        'media',
    ];

    protected $appends = [
        'likedByMe',
        'commentsUrl'
    ];

    protected $withCount = [
        'likes'
    ];

    protected $with = [
        'tags',
    ];

    /**
     * @param int $from
     * @param int $offset
     *
     * @return HasMany
     */
    public function comments(int $from = 0, int $offset = 5) : HasMany
    {
        return $this
            ->hasMany(Comment::class)
            ->orderByDesc('created_at')
            ->where('parent_id', null)
            ->skip($from)
            ->take($offset);
    }

    /**
     * @return HasMany
     */
    public function likes() : HasMany
    {
        return $this->hasMany(QuestionLike::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function media() : Attribute
    {
        $path = storage_path(self::QUESTION_MEDIA_STORAGE);
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

    /**
     * @return string
     */
    protected function getCommentsUrlAttribute() : string
    {
        return route('questionComments', $this->id, false).'?from=0&offset=5';
    }
}
