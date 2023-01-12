<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostRepository $postRepository;

    protected array $user;

    public function __construct(PostRepository $postRepository, Request $request)
    {
        $this->user = $request->user()['data'];
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() : JsonResponse
    {
        return new JsonResponse($this->postRepository->findAll(), JsonResponse::HTTP_OK);
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
            'title'   => ['required', 'string', 'min:3', 'max:100'],
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
        ]);
        $file = $request->file('media');
        $filename = $file->store('/','post');

        $post = new Post();
        $post->title = $request->get('title');
        $post->content = $request->get('content');
        $post->media = $filename;
        $post->user_role = $this->user['role'];
        $post->user_id = $this->user['id'];
        $post->course_id = $this->user['course']['id'];
        $post->save();

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
        $course = $this->postRepository->find($id);
        $course->delete();

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
