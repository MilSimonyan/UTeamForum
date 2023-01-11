<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionRate extends Model
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
     'like',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question() : BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
