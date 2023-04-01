<?php

namespace App\Http\Controllers\Traits;

use App\Models\Tag;
use Illuminate\Support\Collection;

Trait InteractsWithTags
{
    /**
     * @param array $tags
     * @param int   $courseId
     *
     * @return void
     */
    public function checkDbAndSaveNonExistentTags(array $tags, int $courseId) : void
    {
        $tagsFromDb = Tag::whereIn('name', $tags)
            ->get('name')
            ->map(fn ($item) => $item['name'])
            ->values()
            ->toArray();

        $tags = array_diff($tags, $tagsFromDb);

        $tags = collect($tags)->map(function ($item) use ($courseId) {
            return [
                'course_id' => $courseId,
                'name'      => $item,
            ];
        })->toArray();

        Tag::insert($tags);
    }
}