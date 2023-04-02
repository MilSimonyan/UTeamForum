<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Repositories\ForumRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function __construct(
        protected ForumRepository $forumRepository,
        protected TagRepository $tagRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($tagName = $request->get('filter')) {
            return new JsonResponse(
                $this->forumItemsFilteredByTag($request->get('courseId'), $tagName),
                JsonResponse::HTTP_OK
            );
        }

        $from = $request->from ?? 0;
        $offset = $request->offset ?? 10;

        $forumItems = $this->forumRepository->paginateBy(
            [
                'course_id',
                $request->get('courseId')
            ],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/forum?courseId=%d&from=%d&offset=%d',
            $request->get('courseId'),
            $from + $offset,
            10
        );

        if ($forumItems->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'data'    => $forumItems,
            'nextUrl' => $nextUrl
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @param int    $courseId
     * @param string $tagName
     *
     * @return array
     */
    private function forumItemsFilteredByTag(int $courseId, string $tagName): array
    {
        $from = $request->from ?? 0;
        $offset = $request->offset ?? 10;
        $tagId = Tag::where('name', $tagName)->pluck('id')->first();

        $forumItems = $this->tagRepository->paginateForForumItems(
            [
                [
                    'course_id',
                    $courseId
                ],
                [
                    'tag_id',
                    $tagId
                ]
            ],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/tag/%s/forum-items?courseId=%d&from=%d&offset=%d',
            $tagName,
            $courseId,
            $from + $offset,
            10
        );

        if ($forumItems->count() != $offset) {
            $nextUrl = null;
        }

        return [
            'data'    => $forumItems,
            'nextUrl' => $nextUrl
        ];
    }
}
