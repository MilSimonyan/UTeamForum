<?php

namespace App\Repositories;

use App\Models\PostLike;

class PostLikeRepository extends BaseRepository
{
    public function __construct(PostLike $model)
    {
        parent::__construct($model);
    }
}
