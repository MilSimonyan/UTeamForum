<?php

namespace App\Repositories;

use App\Models\CommentRate;

class CommentRateRepository extends BaseRepository
{
    public function __construct(CommentRate $model)
    {
        parent::__construct($model);
    }
}
