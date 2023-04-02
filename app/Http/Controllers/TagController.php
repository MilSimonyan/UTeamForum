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

        $tags = $this->tagRepository->paginateBy([],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/tag?from=%d&offset=%d',
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
}
