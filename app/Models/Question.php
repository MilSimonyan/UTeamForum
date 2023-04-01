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
class Question extends Model
{
    use HasFactory,
        AttributesModifier;

    const QUESTION_MEDIA_STORAGE = 'storage/media/question/';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    private array $user;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'title',
        'content',
        'media',
        'user_role',
        'user_id',
        'course_id',
    ];

    protected $appends = [
        'likedByMe',
        'commentsUrl'
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
    public function comments(int $from = 0, int $offset = 5): HasMany
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
    public function likes(): HasMany
    {
        return $this->hasMany(QuestionLike::class);
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
            get: fn ($value) => asset(self::QUESTION_MEDIA_STORAGE.$value),
        );
    }

    /**
     * modify question author to array
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
     * @return string
     */
    protected function getCommentsUrlAttribute(): string
    {
        return route('questionComments', $this->id, false).'?from=0&offset=5';
    }

    /**
     * @param stdClass $question
     *
     * @return $this
     */
    public function fromStdClass(stdClass $question): static
    {
        $this->id = $question->id;
        $this->title = $question->title;
        $this->content = $question->content;
        $this->media = $question->media;
        $this->author = $question->author;
        $this->likes = $question->likes;
        $this->course_id = $question->course_id;
        $this->refresh()->load('tags');

        return $this;
    }
}
