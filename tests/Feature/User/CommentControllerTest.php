<?php

namespace Tests\Feature\User;

use App\Comment;
use Illuminate\Support\Facades\Route;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('users.comments.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();
        $comments = $user->comments()->saveMany(factory(Comment::class, 5)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->get(route('users.comments.index', [ $user->getKey() ])."?search=lorem");
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.comments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function testCreate()
    {
        if (!Route::has('users.comments.create')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();

        $response = $this->get(route('users.comments.create', [ $user->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.comments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('users.comments.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();

        $response = $this->post(route('users.comments.store', [ $user->getKey() ]), factory(Comment::class)->make([ $user->getForeignKey() => $user->getKey() ])->toArray());

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
        if (!Route::has('users.comments.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $user = factory(User::class)->create();
        $comment = $user->comments()->save(factory(Comment::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->get(route('users.comments.show', [ $user->getKey(), $comment->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.comments.show');
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testEdit()
    {
        if (!Route::has('users.comments.edit')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();
        $comment = $user->comments()->save(factory(Comment::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->get(route('users.comments.edit', [ $user->getKey(), $comment->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.comments.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('users.comments.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();
        $comment = $user->comments()->save(factory(Comment::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->put(route('users.comments.update', [ $user->getKey(), $comment->getKey()  ]), factory(Comment::class)->make([ $user->getForeignKey() => $user->getKey() ])->toArray());
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
        if (!Route::has('users.comments.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //$user = factory(User::class)->create();
        $comment = $user->comments()->save(factory(Comment::class)->make([ $user->getForeignKey() => $user->getKey() ]));

        $response = $this->delete(route('users.comments.destroy', [ $user->getKey(), $comment->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }
}
