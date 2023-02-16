<?php

namespace App\Repositories;

use App\Models\Question;
use App\Models\Tag;

class QuestionRepository extends BaseRepository
{
    public function __construct(Question $model)
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
        Tag::whereNotIn('id', function ($query) {
            $query->select('tag_id')->from('question_tag')
                ->union(function ($query) {
                    $query->select('tag_id')->from('post_tag');
                });
        })->whereIn('id', $tagIds)->delete();
    }
}
