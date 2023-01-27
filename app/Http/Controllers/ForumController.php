<?php

namespace App\Http\Controllers;

use App\Repositories\ForumRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    protected ForumRepository $forumRepository;

    public function __construct(ForumRepository $forumRepository)
    {
        $this->forumRepository = $forumRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        $from = $request->from ?? 0;
        $offset = $request->offset ?? 10;

        $forumItems = $this->forumRepository->paginateBy(
            [
                'course_id',
                $request->courseId
            ],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/forum?courseId=%d&from=%d&offset=%d',
            $request->courseId,
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
}
