<?php

namespace App\Policies;

use App\User;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Post Policy
 */
class PostPolicy
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
     * Determine whether the user can view the post.
     *
     * @param User $user
     * @param Post $post
     * @param null $parent
     * @return mixed
     */
    public function view(User $user, Post $post, $parent = null)
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
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Post $post
     * @param null $parent
     * @return mixed
     */
    public function update(User $user, Post $post, $parent = null)
    {
        return $user->id == $post->user_id;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param User $user
     * @param Post $post
     * @param null $parent
     * @return mixed
     */
    public function delete(User $user, Post $post, $parent = null)
    {
        return $user->id == $post->user_id;
    }
}
