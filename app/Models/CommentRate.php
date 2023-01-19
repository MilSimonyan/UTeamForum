<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $commentId
 * @property string  $userRole
 * @property integer $userId
 * @property integer $value
 */
class CommentRate extends Model
{
    use HasFactory,
        AttributesModifier;
}
