<?php

namespace App\Gates;

use Error;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class TagGate
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function indexForumItems(Authenticatable $user) : bool
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
    public function storeTag(Authenticatable $user) : bool
    {
        try {
            return !$user->getCoursesIds()->intersect(app()->request->get('courseId'))->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }
}
