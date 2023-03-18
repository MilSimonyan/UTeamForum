<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

/**
 * @property string  $content
 * @property string  $media
 * @property string  $userRole
 * @property integer $userId
 * @property integer $parentId
 * @property integer $questionId
 */
class Comment extends Model
{
    use HasFactory,
        AttributesModifier;

    const COMMENT_MEDIA_STORAGE = 'storage/media/comment/';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    private array $user;

    protected $fillable = [
        'content',
        'rate'
    ];

    protected $appends = [
        'ratedByMe',
        'rateValue',
        'user'
    ];

    protected $with = [
        'children'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() : HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent() : HasOne
    {
        return $this->hasOne(Comment::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question() : BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return HasMany
     */
    public function rates() : HasMany
    {
        return $this->hasMany(CommentRate::class);
    }

    /**
     * @return int
     */
    protected function getRatedByMeAttribute() : int
    {
        return $this
            ->rates()
            ->where('user_id', Auth::user()->getId())
            ->where('user_role', Auth::user()->getRole())->first()->value
            ?? 0;
    }

    /**
     * @return int
     */
    protected function getRateValueAttribute() : int
    {
        return $this->rates()->sum('value') ?? 0;
    }

    /**
     * added absolute url for media files
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function media() : Attribute
    {
        return Attribute::make(
            get: fn($value) => asset(self::COMMENT_MEDIA_STORAGE.$value),
        );
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
            'id'        => $this->userId,
            'firstName' => auth()->user()->getFirstName(),
            'lastName'  => auth()->user()->getLastName(),
            'role'      => $this->userRole
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
}
