<?php

namespace App\Http\Controllers;

use App\Models\QuestionLike;
use App\Repositories\QuestionLikeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionLikeController extends Controller
{
    protected QuestionLikeRepository $questionRepository;

    public function __construct(QuestionLikeRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * Toggle a Question like resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request) : JsonResponse
    {
        $this->validate($request, [
            'questionId' => ['required', 'int', 'exists:questions,id'],
        ]);

        $likedByMe = $this->questionRepository->findOneBy(
            [
                ['question_id', $request->get('questionId')],
                ['user_id', Auth::user()->getId()],
                ['user_role', Auth::user()->getRole()]
            ],
            false
        );

        if (!$likedByMe) {
            $questionLike = new QuestionLike();
            $questionLike->questionId = $request->get('questionId');
            $questionLike->user_role = $request->user()->getRole();
            $questionLike->user_id = $request->user()->getId();
            $questionLike->save();

            return new JsonResponse(true, JsonResponse::HTTP_OK);
        }

        $likedByMe->delete();

        return new JsonResponse(false, JsonResponse::HTTP_OK);
    }
}
