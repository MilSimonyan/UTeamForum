<?php

namespace App\Repositories;

use App\Models\QuestionLike;

class QuestionLikeRepository extends BaseRepository
{
    public function __construct(QuestionLike $model)
    {
        parent::__construct($model);
    }
}
