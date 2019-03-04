<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Comment Policy
 */
class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view list of model.
     *
     * @param User $user
     * @param null $parent
     * @return mixed
     */
    public function index(User $user, $parent = null)
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     *
     * @param User $user
     * @param Comment $comment
     * @param null $parent
     * @return mixed
     */
    public function view(User $user, Comment $comment, $parent = null)
    {
        return true;
    }

    /**
     * Determine whether the user can create dummyPluralModel.
     *
     * @param User $user
     * @param null $parent
     * @return mixed
     */
    public function create(User $user, $parent = null)
    {
        return true;
    }

    /**
     * Determine whether the user can update the comment.
     *
     * @param User $user
     * @param Comment $comment
     * @param null $parent
     * @return mixed
     */
    public function update(User $user, Comment $comment, $parent = null)
    {
        return $user->id == $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * @param User $user
     * @param Comment $comment
     * @param null $parent
     * @return mixed
     */
    public function delete(User $user, Comment $comment, $parent = null)
    {
        return $user->id == $comment->user_id;
    }
}
