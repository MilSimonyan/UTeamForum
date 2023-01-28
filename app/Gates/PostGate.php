<?php

namespace App\Gates;

use App\Models\Post;
use Error;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class PostGate
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function indexPost(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(app()->request->courseId)->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function showPost(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(Post::find(app()->request->id)->courseId)->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function likePost(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(Post::find(app()->request->postId)->courseId)->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function updatePost(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(Post::find(app()->request->id)->courseId)->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function storePost(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(app()->request->get('courseId'))->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function destroyPost(Authenticatable $user) : bool
    {
        try {
            return !Post::where('id', app()->request->id)
                ->where('user_id', $user->getId())
                ->where('user_role', $user->getRole())
                ->get()->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }
}
