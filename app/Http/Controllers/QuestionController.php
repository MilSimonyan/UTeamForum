<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Repositories\QuestionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    protected QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
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
        return new JsonResponse(
            $this->questionRepository->findManyBy(
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
        return new JsonResponse($this->questionRepository->find($id), JsonResponse::HTTP_OK);
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
            'content'  => ['string', 'min:3', 'max:3000'],
            'media'    => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
            'tags'     => ['array', 'exists:tags,id'],
            'courseId' => ['required', 'integer'],
        ]);

        $file = $request->file('media');
        $filename = $file->store('/', 'question');

        $question = new Question();
        $question->title = $request->get('title');
        $question->content = $request->get('content');
        $question->media = $filename;
        $question->user_role = $request->user()->getRole();
        $question->user_id = $request->user()->getId();
        $question->course_id = $request->get('courseId');
        $question->save();
        $question->tags()->sync($request->get('tags'));
        $question->refresh()->load('tags');

        return new JsonResponse($question, JsonResponse::HTTP_CREATED);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'title'   => ['string', 'min:3', 'max:100'],
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
            'tags'    => ['array', 'exists:tags,id'],
        ]);

        if ($file = $request->file('media')) {
            $filename = $file->store('/', 'question');
        }

        /** @var Question $question */
        $question = $this->questionRepository->find($id);
        $question->title = $request->get('title', $question->title);
        $question->content = $request->get('content', $question->content);
        $question->media = $filename ?? null;
        $question->user_role = $request->user()->getRole();
        $question->user_id = $request->user()->getId();
        $question->course_id = $request->get('courseId', $question->course_id);
        $question->save();
        $question->tags()->sync($request->get('tags', $question->tags()->get()));
        $question->refresh()->load('tags');

        return new JsonResponse($question, JsonResponse::HTTP_CREATED);
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
        $course = $this->questionRepository->find($id);
        $course->delete();

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
