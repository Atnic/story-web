<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * User Policy
 */
class UserPolicy
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
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @param null $parent
     * @return mixed
     */
    public function view(User $user, User $model, $parent = null)
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
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @param null $parent
     * @return mixed
     */
    public function update(User $user, User $model, $parent = null)
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @param null $parent
     * @return mixed
     */
    public function delete(User $user, User $model, $parent = null)
    {
        return $user->id == $model->id;;
    }
}
