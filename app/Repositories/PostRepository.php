<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Tag;

class PostRepository extends BaseRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $tagIds
     *
     * @return void
     */
    public function logicWhenTagShouldRemoved(array $tagIds) : void
    {
        Tag::whereIn('id', $tagIds)->delete();
    }
}
