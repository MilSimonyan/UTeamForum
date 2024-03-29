<?php

namespace App\Gates;

use App\Models\Question;
use Error;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class QuestionGate
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function indexQuestion(Authenticatable $user): bool
    {
        try {
            return !$user
                ->getCoursesIds()
                ->intersect(app()->request->courseId)
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
    public function showQuestion(Authenticatable $user): bool
    {
        try {
            return !$user
                ->getCoursesIds()
                ->intersect(Question::find(app()->request->id)->courseId)
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
    public function likeQuestion(Authenticatable $user): bool
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
    public function updateQuestion(Authenticatable $user): bool
    {
        try {
            /** @var Question $question */
            $question = Question::find(app()->request->id);

            return $user->getId() === $question->author['id'] &&
                $user->getRole() === $question->author['role'] &&
                !$question->comments()->count();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function storeQuestion(Authenticatable $user): bool
    {
        try {
            return !$user
                ->getCoursesIds()
                ->intersect(app()->request->get('courseId'))
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
    public function destroyQuestion(Authenticatable $user): bool
    {
        try {
            return !Question::where('id', app()->request->id)
                ->where('user_id', $user->getId())
                ->where('user_role', $user->getRole())
                ->get()->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }
}
