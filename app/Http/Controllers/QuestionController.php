<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Tag;
use App\Repositories\QuestionRepository;
use App\Services\ImageAdapter\ImageAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Traits\InteractsWithTags;

class QuestionController extends Controller
{
    use InteractsWithTags;

    public function __construct(
        protected QuestionRepository $questionRepository,
        protected ImageAdapter $imageAdapter
    ) {
        $this->imageAdapter->supportHeight = 500;
        $this->imageAdapter->supportWidth = 900;
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

        $questions = $this->questionRepository->paginateBy(
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
            '/api/question?courseId=%dfrom=%d&offset=%d',
            $request->courseId,
            $from + $offset,
            10
        );

        if ($questions->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'questions' => $questions,
            'nextUrl'   => $nextUrl
        ],
            JsonResponse::HTTP_OK);
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
        return new JsonResponse($this->questionRepository->find($id), JsonResponse::HTTP_OK);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function comments(Request $request, int $id): JsonResponse
    {
        $from = $request->from ?? 0;
        $offset = $request->offset ?? 5;

        $comments = $this
            ->questionRepository
            ->find($id)
            ->comments($from, $offset)
            ->get();

        $nextUrl = sprintf(
            '/api/question/%d/comments?from=%d&offset=%d',
            $id,
            $from + $offset,
            5
        );

        if ($comments->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'comments' => $comments,
            'nextUrl'  => $nextUrl
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \JsonException
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
            Storage::disk('question');
            $image = $this->imageAdapter->make($file);
            $this->imageAdapter->resize($image, $image->width(), $image->height());
            $filename = hash('sha256', $image->filename).'.'.$file->extension();
            $image->save(storage_path('app/media/question/'.$filename));
        }

        if ($request->get('tags')) {
            $requestTags = array_unique($request->get('tags'));
            $this->checkDbAndSaveNonExistentTags($requestTags, $request->get('courseId'));
            $tagIds = Tag::whereIn('name', $requestTags)->pluck('id')->toArray();
        }

        $question = new Question();
        $question->title = $request->get('title');
        $question->content = $request->get('content');
        $question->media = $filename ?? null;
        $question->user_role = $request->user()->getRole();
        $question->user_id = $request->user()->getId();
        $question->author = json_encode([
            'id'        => $question->user_id,
            'firstName' => $request->user()->getFirstName(),
            'lastName'  => $request->user()->getLastName(),
            'role'      => $question->user_role,
            'thumbnail' => $request->user()->getThumbnail()
        ], JSON_THROW_ON_ERROR);
        $question->course_id = $request->get('courseId');
        $question->likes = 0;

        $question->save();
        $question->tags()->sync($tagIds ?? null);
        $question->refresh()->load('tags');

        return new JsonResponse($question, JsonResponse::HTTP_CREATED);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'title'   => ['string', 'min:3', 'max:100'],
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
            'tags'    => ['array'],
        ]);

        /** @var Question $question */
        $question = $this->questionRepository->find($id);

        if ($file = $request->file('media')) {
            if ($question->media) {
                Storage::disk('question')->delete($question->media);
            }

            Storage::disk('question');
            $image = $this->imageAdapter->make($file);
            $this->imageAdapter->resize($image, $image->width(), $image->height());
            $filename = hash('sha256', $image->filename).'.'.$file->extension();
            $image->save(storage_path('app/media/question/'.$filename));
        }

        $requestTags = array_unique($request->get('tags', []));
        $questionTags = $question->tags()->get()->pluck('id')->toArray();

        $this->checkDbAndSaveNonExistentTags($requestTags, $question->course_id);
        $requestTags = Tag::whereIn('name', $requestTags)->pluck('id')->toArray();

        $difference = array_diff($questionTags, $requestTags);

        $question->title = $request->get('title', $question->title);
        $question->content = $request->get('content', $question->content);
        $question->media = $filename ?? pathinfo($question->media)['basename'];
        $question->user_role = $request->user()->getRole();
        $question->user_id = $request->user()->getId();
        $question->save();
        $question->tags()->sync($requestTags);
        $question->refresh()->load('tags', 'comments');

        $this->questionRepository->logicWhenTagShouldRemoved($difference);

        return new JsonResponse($question, JsonResponse::HTTP_CREATED);
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
        /** @var Question $question */
        $question = $this->questionRepository->find($id);

        if ($question->media) {
            Storage::disk('question')->delete($question->media);
        }

        $question->delete();

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
