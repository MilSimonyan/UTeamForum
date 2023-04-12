<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentRate;
use App\Repositories\CommentRateRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentRateController extends Controller
{
    protected CommentRateRepository $commentRateRepository;

    public function __construct(CommentRateRepository $commentRateRepository)
    {
        $this->commentRateRepository = $commentRateRepository;
    }

    /**
     * Toggle a Post like resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        $this->validate($request, [
            'commentId' => ['required', 'int', 'exists:comments,id'],
            'value'     => ['required', 'int', 'in:-1,0,1'],
        ], [
            'value.in' => 'The value must be -1, 0, or 1.',
        ]);

        $rateByMe = $this->commentRateRepository->findOneBy(
            [
                ['comment_id', $request->get('commentId')],
                ['user_id', Auth::user()->getId()],
                ['user_role', Auth::user()->getRole()]
            ],
            false
        );

        $needToUpdateCommentRate = true;

        if ($rateByMe) {
            $commentRate = $rateByMe;

            if ($commentRate->value === $request->get('value')) {
                $needToUpdateCommentRate = false;
            }

            if ($request->get('value') === 0) {
                $comment = Comment::find($commentRate->commentId);
                $comment->rate -= $commentRate->value;
                $comment->save();

                $rateByMe->delete();

                return new JsonResponse(0, JsonResponse::HTTP_OK);
            }
        } else {
            if ($request->get('value') === 0) {
                return new JsonResponse(0, JsonResponse::HTTP_OK);
            }
        }

        $commentRate = $commentRate ?? new CommentRate();
        $commentRate->commentId = $request->get('commentId');
        $commentRate->value = $request->get('value');
        $commentRate->userRole = $request->user()->getRole();
        $commentRate->userId = $request->user()->getId();
        $commentRate->save();

        if ($needToUpdateCommentRate) {
            $comment = Comment::find($commentRate->commentId);
            $comment->rate = 2 * $commentRate->value + $comment->rate;
            $comment->save();
        }

        return new JsonResponse($commentRate->value, JsonResponse::HTTP_OK);
    }
}
