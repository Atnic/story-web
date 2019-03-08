<?php

namespace App\Http\Controllers\User;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;

/**
 * PostController
 */
class PostController extends Controller
{
    /**
     * Relations
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Post $post
     * @return array
     */
    public static function relations(Request $request = null, User $user = null, Post $post = null)
    {
        return [
            'user' => UserController::relations($request, $user)['user'],
            'post' => [
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
     * @param Post $post
     * @return array
     */
    public static function visibles(Request $request = null, User $user = null, Post $post = null)
    {
        return [
            'parent' => [
                'user' => UserController::visibles($request, $user)['show']['user']
            ],
            'index' => [
                'post' => [
                    [ 'name' => 'post', 'label' => ucwords(__('posts.post')) ],
                    [ 'name' => 'created_at', 'label' => ucwords(__('posts.created_at')), 'class' => 'text-nowrap' ],
                ]
            ],
            'show' => [
                'post' => [
                    [ 'name' => 'post', 'label' => ucwords(__('posts.post')) ],
                    [ 'name' => 'created_at', 'label' => ucwords(__('posts.created_at')), 'class' => 'text-nowrap' ],
                ]
            ]
        ];
    }

    /**
     * Fields
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Post $post
     * @return array
     */
    public static function fields(Request $request = null, User $user = null, Post $post = null)
    {
        return [
            'create' => [
                'post' => [
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'name', 'label' => ucwords(__('posts.name')), 'required' => true ],
                ]
            ],
            'edit' => [
                'post' => [
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'name', 'label' => ucwords(__('posts.name')) ],
                ]
            ]
        ];
    }

    /**
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Post $post
     * @return array
     */
    public static function rules(Request $request = null, User $user = null, Post $post = null)
    {
        return [
            'store' => [
                'post' => 'required|string',
            ],
            'update' => [
                'post' => 'string',
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
        $posts = Post::filter()
            ->where((new Post)->qualifyColumn($user->getForeignKey()), $user->getKey())
            ->paginate()->appends(request()->query());
        $this->authorize('index', [ 'App\Post', $user ]);

        return response()->view('users.posts.index', [
            'user' => $user,
            'posts' => $posts,
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
        $this->authorize('create', [ 'App\Post', $user ]);

        return response()->view('users.posts.create', [
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
        $this->authorize('create', [ 'App\Post', $user ]);
        $request->validate(self::rules($request, $user)['store']);

        $post = new Post;
        foreach (self::rules($request, $user)['store'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $post->{$key} = $request->file($key)->store('posts');
                } elseif ($request->exists($key)) {
                    $post->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $post->{$key} = $request->{$key};
            }
        }
        $post->user()->associate($user);
        $post->save();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('users.posts.show', [ $user->getKey(), $post->getKey() ]);

        return $response->withInput([
            $user->getForeignKey() => $user->getKey(),
            $post->getForeignKey() => $post->getKey(),
        ])->with('status', __('Success'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());
        $this->authorize('view', [ $post, $user ]);

        return response()->view('users.posts.show', [
            'user' => $user,
            'post' => $post,
            'relations' => self::relations(request(), $user, $post),
            'visibles' => array_merge(self::visibles(request(), $user, $post)['parent'], self::visibles(request(), $user, $post)['show'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());
        $this->authorize('update', [ $post, $user ]);

        return response()->view('users.posts.edit', [
            'user' => $user,
            'post' => $post,
            'relations' => self::relations(request(), $user, $post),
            'visibles' => self::visibles(request(), $user, $post)['parent'],
            'fields' => self::fields(request(), $user, $post)['edit']
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());

        $this->authorize('update', [ $post, $user ]);
        $request->validate(self::rules($request, $user, $post)['update']);

        foreach (self::rules($request, $user, $post)['update'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $post->{$key} = $request->file($key)->store('posts');
                } elseif ($request->exists($key)) {
                    $post->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $post->{$key} = $request->{$key};
            }
        }
        $post->user()->associate($user);
        $post->save();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('users.posts.show', [ $user->getKey(), $post->getKey() ]);

        return $response->withInput([
            $user->getForeignKey() => $user->getKey(),
            $post->getForeignKey() => $post->getKey(),
        ])->with('status', __('Success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());
        $this->authorize('delete', [ $post, $user ]);
        $post->delete();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()) && !str_contains(request()->redirect, '/'.array_last(explode('.', 'users.posts')).'/'.$post->getKey()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('users.posts.index', $user->getKey());

        return $response->with('status', __('Success'));
    }
}
