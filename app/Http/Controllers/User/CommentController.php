<?php

namespace App\Http\Controllers\User;

use App\Comment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;

/**
 * CommentController
 */
class CommentController extends Controller
{
    /**
     * Relations
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public static function relations(Request $request = null, User $user = null, Comment $comment = null)
    {
        return [
            'user' => UserController::relations($request, $user)['user'],
            'comment' => [
                'belongsToMany' => [], // also for morphToMany
                'hasMany' => [], // also for morphMany, hasManyThrough
                'hasOne' => [], // also for morphOne
            ]
        ];
    }

    /**
     * Visibles
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public static function visibles(Request $request = null, User $user = null, Comment $comment = null)
    {
        return [
            'parent' => [
                'user' => UserController::visibles($request, $user)['show']['user']
            ],
            'index' => [
                'comment' => [
                    [ 'name' => 'comment', 'label' => ucwords(__('comments.comment')) ],
                    [ 'name' => 'created_at', 'label' => ucwords(__('comments.created_at')), 'class' => 'text-nowrap' ],
                ]
            ],
            'show' => [
                'comment' => [
                    [ 'name' => 'comment', 'label' => ucwords(__('comments.comment')) ],
                    [ 'name' => 'created_at', 'label' => ucwords(__('comments.created_at')), 'class' => 'text-nowrap' ],
                ]
            ]
        ];
    }

    /**
     * Fields
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public static function fields(Request $request = null, User $user = null, Comment $comment = null)
    {
        return [
            'create' => [
                'comment' => [
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'name', 'label' => ucwords(__('comments.name')), 'required' => true ],
                ]
            ],
            'edit' => [
                'comment' => [
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'name', 'label' => ucwords(__('comments.name')) ],
                ]
            ]
        ];
    }

    /**
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public static function rules(Request $request = null, User $user = null, Comment $comment = null)
    {
        return [
            'store' => [
                'name' => 'required|string|max:255',
            ],
            'update' => [
                'name' => 'string|max:255',
            ]
        ];
    }

    /**
    * Instantiate a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(User $user)
    {
        $comments = Comment::filter()
            ->where((new Comment)->qualifyColumn($user->getForeignKey()), $user->getKey())
            ->paginate()->appends(request()->query());
        $this->authorize('index', [ 'App\Comment', $user ]);

        return response()->view('users.comments.index', [
            'user' => $user,
            'comments' => $comments,
            'relations' => self::relations(request(), $user),
            'visibles' => array_merge(self::visibles(request(), $user)['parent'], self::visibles(request(), $user)['index']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(User $user)
    {
        $this->authorize('create', [ 'App\Comment', $user ]);

        return response()->view('users.comments.create', [
            'user' => $user,
            'relations' => self::relations(request(), $user),
            'visibles' => self::visibles(request(), $user)['parent'],
            'fields' => self::fields(request(), $user)['create']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, User $user)
    {
        $this->authorize('create', [ 'App\Comment', $user ]);
        $request->validate(self::rules($request, $user)['store']);

        $comment = new Comment;
        foreach (self::rules($request, $user)['store'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $comment->{$key} = $request->file($key)->store('comments');
                } elseif ($request->exists($key)) {
                    $comment->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $comment->{$key} = $request->{$key};
            }
        }
        $comment->user()->associate($user);
        $comment->save();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('users.comments.show', [ $user->getKey(), $comment->getKey() ]);

        return $response->withInput([
            $user->getForeignKey() => $user->getKey(),
            $comment->getForeignKey() => $comment->getKey(),
        ])->with('status', __('Success'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user, Comment $comment)
    {
        $user->comments()->findOrFail($comment->getKey());
        $this->authorize('view', [ $comment, $user ]);

        return response()->view('users.comments.show', [
            'user' => $user,
            'comment' => $comment,
            'relations' => self::relations(request(), $user, $comment),
            'visibles' => array_merge(self::visibles(request(), $user, $comment)['parent'], self::visibles(request(), $user, $comment)['show'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user, Comment $comment)
    {
        $user->comments()->findOrFail($comment->getKey());
        $this->authorize('update', [ $comment, $user ]);

        return response()->view('users.comments.edit', [
            'user' => $user,
            'comment' => $comment,
            'relations' => self::relations(request(), $user, $comment),
            'visibles' => self::visibles(request(), $user, $comment)['parent'],
            'fields' => self::fields(request(), $user, $comment)['edit']
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user, Comment $comment)
    {
        $user->comments()->findOrFail($comment->getKey());

        $this->authorize('update', [ $comment, $user ]);
        $request->validate(self::rules($request, $user, $comment)['update']);

        foreach (self::rules($request, $user, $comment)['update'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $comment->{$key} = $request->file($key)->store('comments');
                } elseif ($request->exists($key)) {
                    $comment->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $comment->{$key} = $request->{$key};
            }
        }
        $comment->user()->associate($user);
        $comment->save();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('users.comments.show', [ $user->getKey(), $comment->getKey() ]);

        return $response->withInput([
            $user->getForeignKey() => $user->getKey(),
            $comment->getForeignKey() => $comment->getKey(),
        ])->with('status', __('Success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user, Comment $comment)
    {
        $user->comments()->findOrFail($comment->getKey());
        $this->authorize('delete', [ $comment, $user ]);
        $comment->delete();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()) && !str_contains(request()->redirect, '/'.array_last(explode('.', 'users.comments')).'/'.$comment->getKey()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('users.comments.index', $user->getKey());

        return $response->with('status', __('Success'));
    }
}
