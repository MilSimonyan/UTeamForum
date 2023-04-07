<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\InteractsWithTags;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\PostRepository;
use App\Services\ImageAdapter\ImageAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use InteractsWithTags;

    public function __construct(
        protected PostRepository $postRepository,
        protected ImageAdapter $imageAdapter
    ) {
        $this->imageAdapter->supportHeight = 800;
        $this->imageAdapter->supportWidth = 600;
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
        $from = $request->from ?? 0;
        $offset = $request->offset ?? 10;

        $posts = $this->postRepository->paginateBy(
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
            '/api/post?courseId=%d&from=%d&offset=%d',
            $request->courseId,
            $from + $offset,
            10
        );

        if ($posts->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'posts'   => $posts,
            'nextUrl' => $nextUrl
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
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
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title'    => ['required', 'string', 'min:3', 'max:100'],
            'content'  => ['required', 'string', 'min:3', 'max:3000'],
            'media'    => ['mimes:jpg,jpeg,png'],
            'tags'     => ['array'],
            'courseId' => ['required', 'integer'],
        ]);

        if ($file = $request->file('media')) {
            $image = $this->imageAdapter->make($file);
            $this->imageAdapter->resize($image, $image->width(), $image->height());
            $filename = hash('sha256', $image->filename).'.'.$file->extension();
            $image->save(storage_path('/app/media/post/'.$filename));
        }

        if ($request->get('tags')) {
            $requestTags = array_unique($request->get('tags'));
            $this->checkDbAndSaveNonExistentTags($requestTags, $request->get('courseId'));
            $tagIds = Tag::whereIn('name', $requestTags)->pluck('id')->toArray();
        }

        $post = new Post();
        $post->title = $request->get('title');
        $post->content = $request->get('content');
        $post->media = $filename ?? null;
        $post->user_role = $request->user()->getRole();
        $post->user_id = $request->user()->getId();
        $post->author = json_encode([
            'id'        => $post->user_id,
            'firstName' => $request->user()->getFirstName(),
            'lastName'  => $request->user()->getLastName(),
            'role'      => $post->user_role
            //            'thumbnail' => auth()->user()->getThumbnail() TODO after added from user
        ]);
        $post->course_id = $request->get('courseId');
        $post->likes = 0;

        $post->save();
        $post->tags()->sync($tagIds ?? null);
        $post->refresh()->load('tags');

        return new JsonResponse($post, JsonResponse::HTTP_CREATED);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'title'   => ['string', 'min:3', 'max:100'],
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png'],
            'tags'    => ['array'],
        ]);

        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if ($file = $request->file('media')) {
            if ($post->media) {
                Storage::disk('post')->delete($post->media);
            }

            $filename = $file->store('/', 'post');
        }

        $requestTags = array_unique($request->get('tags'));
        $postTags = $post->tags()->get()->pluck('id')->toArray();
        $this->checkDbAndSaveNonExistentTags($requestTags, $post->course_id);
        $requestTags = Tag::whereIn('name', $requestTags)->pluck('id')->toArray();


        $difference = array_diff($postTags, $requestTags);

        $post->title = $request->get('title', $post->title);
        $post->content = $request->get('content', $post->content);
        $post->media = $filename ?? null;
        $post->user_role = $request->user()->getRole();
        $post->user_id = $request->user()->getId();
        $post->course_id = $request->get('courseId', $post->course_id);
        $post->save();
        $post->tags()->sync($requestTags);
        $post->refresh()->load('tags');

        $this->postRepository->logicWhenTagShouldRemoved($difference);

        return new JsonResponse($post, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if ($post->media) {
            Storage::disk('post')->delete($post->media);
        }

        $post->delete();

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
