<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ForumRepository extends BaseRepository
{
    protected QuestionRepository $questionRepository;
    protected PostRepository $postRepository;

    public function __construct(Comment $model, PostRepository $postRepository, QuestionRepository $questionRepository)
    {
        $this->postRepository = $postRepository;
        $this->questionRepository = $questionRepository;

        parent::__construct($model);
    }

    /**
     * @param array $criteria
     * @param int   $from
     * @param int   $offset
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function paginateBy(array $criteria, int $from, int $offset) : Collection|array
    {
        $questions = DB::table('questions')->select(
            DB::raw("'Question' as model"),
            'questions.*'
        );

        $posts = DB::table('posts')->select(
            DB::raw("'Post' as model"),
            'posts.*'
        );

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

//        $forumItems = DB::table('questions')->select(
//            DB::raw("'Question' as model"),
//            'questions.*'
//        )->where(...$criteria)
//            ->union(
//                DB::table('posts')->select(
//                    DB::raw("'Post' as model"),
//                    'posts.*'
//                )->where(...$criteria)
//            )->orderByDesc('created_at')->skip($from)->take($offset)->get();

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
