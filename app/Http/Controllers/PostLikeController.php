<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use App\Repositories\PostLikeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostLikeController extends Controller
{
    protected PostLikeRepository $likeRepository;

    public function __construct(PostLikeRepository $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    /**
     * Toggle a Post like resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request) : JsonResponse
    {
        $this->validate($request, [
            'postId' => ['required', 'int', 'exists:posts,id'],
        ]);

        $likedByMe = $this->likeRepository->findOneBy(
            [
                ['post_id' , $request->get('postId')],
                ['user_id' , Auth::user()->getId()],
                ['user_role' , Auth::user()->getRole()]
            ],
            false
        );

        if (!$likedByMe) {
            $postLike = new PostLike();
            $postLike->postId = $request->get('postId');
            $postLike->user_role = $request->user()->getRole();
            $postLike->user_id = $request->user()->getId();
            $postLike->save();

            return new JsonResponse(true, JsonResponse::HTTP_OK);
        }

        $likedByMe->delete();

        return new JsonResponse(false, JsonResponse::HTTP_OK);
    }
}
