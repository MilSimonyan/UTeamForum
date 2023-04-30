<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Services\ImageAdapter\ImageAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function __construct(
        protected CommentRepository $commentRepository,
        protected ImageAdapter $imageAdapter
    ) {
        $this->imageAdapter->supportHeight = 600;
        $this->imageAdapter->supportWidth = 1050;
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
            'content'    => ['required', 'string', 'min:3', 'max:3000'],
            'questionId' => ['required', 'exists:questions,id'],
            'media'      => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
            'parentId'   => ['integer', 'exists:comments,id'],
        ]);

        if ($file = $request->file('media')) {
            Storage::disk('comment');
            $image = $this->imageAdapter->make($file);
            $this->imageAdapter->resize($image, $image->width(), $image->height());

            $filename = hash('sha256', $image->filename).'.'.$file->extension();

            $image->save(storage_path('/app/media/comment/'.$filename));
        }

        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->questionId = $request->get('questionId');
        $comment->media = $filename ?? null;
        $comment->userRole = $request->user()->getRole();
        $comment->author = json_encode([
            'id'        => $comment->user_id,
            'firstName' => $request->user()->getFirstName(),
            'lastName'  => $request->user()->getLastName(),
            'role'      => $comment->user_role
            //            'thumbnail' => auth()->user()->getThumbnail() TODO after added from user
        ]);
        $comment->userId = $request->user()->getId();
        $comment->parentId = $request->get('parentId');
        $comment->rate = 0;

        $comment->save();

        return new JsonResponse($comment, JsonResponse::HTTP_CREATED);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id) : JsonResponse
    {
        $this->validate($request, [
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png,gif,mp4,mov,ogg'],
        ]);

        /** @var Comment $comment */
        $comment = $this->commentRepository->find($id);

        if ($file = $request->file('media')) {
            if ($comment->media) {
                Storage::disk('comment')->delete($comment->media);
            }

            $filename = $file->store('/', 'comment');
        }

        $comment->content = $request->get('content', $comment->content);
        $comment->media = $filename ?? $comment->media;
        $comment->userRole = $request->user()->getRole();
        $comment->userId = $request->user()->getId();

        $comment->save();
        $comment->refresh();

        return new JsonResponse($comment, JsonResponse::HTTP_OK);
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
        /** @var Comment $comment */
        $comment = $this->commentRepository->find($id);

        if ($comment->media)
            Storage::disk('comment')->delete($comment->media);

        $comment->delete();

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
