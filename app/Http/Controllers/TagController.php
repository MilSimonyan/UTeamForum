<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * @var \App\Repositories\TagRepository
     */
    protected TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
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

        $tags = $this->tagRepository->paginateBy(
            [
                [
                    'course_id',
                    $request->courseId
                ]
            ],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/tag?courseId=%d&from=%d&offset=%d',
            $request->courseId,
            $from + $offset,
            10
        );

        if ($tags->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'tags'    => $tags,
            'nextUrl' => $nextUrl
        ],
            JsonResponse::HTTP_OK);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forumItems(Request $request, int $id) : JsonResponse
    {
        $from = $request->from ?? 0;
        $offset = $request->offset ?? 10;

        $forumItems = $this->tagRepository->paginateBy(
            [
                [
                    'course_id',
                    $request->courseId
                ],
                [
                    'tag_id',
                    $id
                ]
            ],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/tag/%d/forum-items?courseId=%d&from=%d&offset=%d',
            $id,
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request) : JsonResponse
    {
        $this->validate($request, [
            'name'     => ['required', 'string', 'min:2', 'max:30', 'unique:tags,name'],
            'courseId' => ['required', 'int'],
        ]);

        $tag = new Tag();
        $tag->name = $request->get('name');
        $tag->courseId = $request->get('courseId');
        $tag->save();

        return new JsonResponse($tag, JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id) : JsonResponse
    {
        return new JsonResponse($this->tagRepository->find($id), JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id) : JsonResponse
    {
        $tag = $this->tagRepository->find($id);
        $tag->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
