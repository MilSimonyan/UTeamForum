<?php

namespace App\Gates;

use App\Models\Comment;
use App\Models\Question;
use Error;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class CommentGate
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function rateComment(Authenticatable $user): bool
    {
        try {
            return !$user
                ->getCoursesIds()
                ->intersect(
                    Comment::find(app()->request->commentId)
                        ->question()
                        ->first()
                        ->courseId
                )
                ->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function showComments(Authenticatable $user): bool
    {
        try {
            return !$user
                ->getCoursesIds()
                ->intersect(
                    Question::find(app()->request->id)
                        ->courseId
                )->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function updateComment(Authenticatable $user): bool
    {
        try {
            /** @var Comment $comment */
            $comments = Comment::where('parent_id', app()->request->id)->where(
                'question_id',
                app()->request->questionId
            );
            $comment = Comment::find(app()->request->id);

            return !$user
                    ->getCoursesIds()
                    ->intersect($comment->question()->first()->courseId)
                    ->isEmpty() &&
                !$comments->count();
        } catch (Exception|Error $e) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function storeComment(Authenticatable $user): bool
    {
        try {
            return !$user
                ->getCoursesIds()
                ->intersect(Question::find(app()->request->questionId)->courseId)
                ->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function destroyComment(Authenticatable $user): bool
    {
        try {
            return !Comment::where('id', app()->request->id)
                ->where('user_id', $user->getId())
                ->where('user_role', $user->getRole())
                ->get()->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }
}
