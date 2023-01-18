<?php

namespace App\Gates;

use App\Models\Question;
use Illuminate\Contracts\Auth\Authenticatable;

class QuestionGate
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function showQuestion(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(Question::find(app()->request->id)->courseId)->isEmpty();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function likeQuestion(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(Question::find(app()->request->questionId)->courseId)->isEmpty();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function updateQuestion(Authenticatable $user) : bool
    {
        try {
            $question = Question::find(app()->request->id);

            return !$user->getCoursesIds()->intersect($question->courseId)->isEmpty() &&
                $question->created_at->gt(now()->subMinutes(5));
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function storeQuestion(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(app()->request->get('courseId'))->isEmpty();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function destroyQuestion(Authenticatable $user) : bool
    {
        try {
            return !Question::where('id', app()->request->id)
                ->where('user_id', $user->getId())
                ->where('user_role', $user->getRole())
                ->get()->isEmpty();
        } catch (\Exception) {
            return false;
        }
    }
}
