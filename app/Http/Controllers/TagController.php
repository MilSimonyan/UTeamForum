<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Repositories\ForumRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * @var \App\Repositories\TagRepository
     */
    protected TagRepository $tagRepository;
    /**
     * @var \App\Repositories\ForumRepository
     */
    private ForumRepository $forumRepository;

    public function __construct(TagRepository $tagRepository, ForumRepository $forumRepository)
    {
        $this->tagRepository = $tagRepository;
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

        $items = $this->forumRepository->paginateBy(
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
            '/api/tag?from=%d&offset=%d',
            $from + $offset,
            10
        );

        if ($items->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'questions' => $items,
            'nextUrl'   => $nextUrl
        ],
            JsonResponse::HTTP_OK);
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
            'name' => ['required', 'string', 'min:2', 'max:30', 'unique:tags,name']
        ]);

        $tag = new Tag();
        $tag->name = $request->get('name');
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
