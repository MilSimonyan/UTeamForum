<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Question;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TagRepository extends BaseRepository
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $criteria
     * @param int   $from
     * @param int   $offset
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function paginateForForumItems(array $criteria, int $from, int $offset) : Collection|array
    {
        $questions = DB::table('questions')->select(
            DB::raw("'Question' as model"),
            'questions.*'
        )->join('question_tag', 'questions.id', '=', 'question_tag.question_id');

        $posts = DB::table('posts')->select(
            DB::raw("'Post' as model"),
            'posts.*'
        )->join('post_tag', 'posts.id', '=', 'post_tag.post_id');

        if (is_array($criteria[0])) {
            foreach ($criteria as $criterion) {
                $questions->where(...$criterion);
                $posts->where(...$criterion);
            }
        } else {
            $questions->where(...$criteria);
            $posts->where(...$criteria);
        }

        $forumItems = $questions
            ->union(
                $posts
            )->orderByDesc('created_at')->skip($from)->take($offset)->get();

        return $forumItems->map(function ($item) {
            $post = new Post();
            $question = new Question();
            if ($item->model === 'Question') {
                $entity = $question->fromStdClass($item);
            } else {
                $entity = $post->fromStdClass($item);
            }

            return $entity;
        });
    }
}
