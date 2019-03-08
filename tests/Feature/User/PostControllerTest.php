<?php

namespace Tests\Feature\User;

use App\Post;
use Illuminate\Support\Facades\Route;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('users.posts.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();
        $posts = $user->posts()->saveMany(factory(Post::class, 5)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->get(route('users.posts.index', [ $user->getKey() ])."?search=lorem");
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.posts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function testCreate()
    {
        if (!Route::has('users.posts.create')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();

        $response = $this->get(route('users.posts.create', [ $user->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('users.posts.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();

        $response = $this->post(route('users.posts.store', [ $user->getKey() ]), factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ])->toArray());
        if ($response->getStatusCode() == 422) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return json_encode(session()->all(), JSON_PRETTY_PRINT); });
            return;
        }
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testShow()
    {
        if (!Route::has('users.posts.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->get(route('users.posts.show', [ $user->getKey(), $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.posts.show');
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testEdit()
    {
        if (!Route::has('users.posts.edit')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->get(route('users.posts.edit', [ $user->getKey(), $post->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.posts.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('users.posts.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->put(route('users.posts.update', [ $user->getKey(), $post->getKey()  ]), factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ])->toArray());
        if ($response->getStatusCode() == 422) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return json_encode(session()->all(), JSON_PRETTY_PRINT); });
            return;
        }
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     * @throws \Exception
     */
    public function testDestroy()
    {
        if (!Route::has('users.posts.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->delete(route('users.posts.destroy', [ $user->getKey(), $post->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }
}
