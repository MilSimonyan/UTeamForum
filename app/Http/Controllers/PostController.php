<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
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
        return new JsonResponse($this->postRepository->findManyBy(
            [
                [
                    'course_id',
                    $request->user()->getCoursesIds()->toArray()
                ]
            ]
        ), JsonResponse::HTTP_OK);
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
        return new JsonResponse($this->postRepository->find($id), JsonResponse::HTTP_OK);
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
            'title'    => ['required', 'string', 'min:3', 'max:100'],
            'content'  => ['required', 'string', 'min:3', 'max:3000'],
            'media'    => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
            'tags'     => ['array', 'exists:tags,id'],
            'courseId' => ['required', 'integer'],
        ]);

        if ($file = $request->file('media'))
            $filename = $file->store('/', 'post');

        $post = new Post();
        $post->title = $request->get('title');
        $post->content = $request->get('content');
        $post->media = $filename ?? null;
        $post->user_role = $request->user()->getRole();
        $post->user_id = $request->user()->getId();
        $post->course_id = $request->get('courseId');
        $post->save();
        $post->tags()->sync($request->get('tags'));
        $post->refresh()->load('tags');

        return new JsonResponse($post, JsonResponse::HTTP_CREATED);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id) : JsonResponse
    {
        $this->validate($request, [
            'title'   => ['string', 'min:3', 'max:100'],
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
            'tags'    => ['array', 'exists:tags,id'],
        ]);

        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if ($file = $request->file('media')) {
            if ($post->media) {
                Storage::disk('post')->delete($post->media);
            }

            $filename = $file->store('/', 'post');
        }

        $post->title = $request->get('title', $post->title);
        $post->content = $request->get('content', $post->content);
        $post->media = $filename ?? null;
        $post->user_role = $request->user()->getRole();
        $post->user_id = $request->user()->getId();
        $post->course_id = $request->get('courseId', $post->course_id);
        $post->save();
        $post->tags()->sync($request->get('tags', $post->tags()->get()));
        $post->refresh()->load('tags');

        return new JsonResponse($post, JsonResponse::HTTP_CREATED);
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
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if ($post->media)
            Storage::disk('post')->delete($post->media);

        $post->delete();

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
