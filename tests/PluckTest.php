<?php

namespace Rennokki\QueryCache\Test;

use Rennokki\QueryCache\Test\Models\Post;

class PluckTest extends TestCase
{
    public function test_pluck_is_cached()
    {
        factory(Post::class, 3)->create();

        $names = Post::cacheFor(now()->addHours(1))->pluck('name');

        $this->assertSame(0, $this->countQueries(function () use ($names) {
            $this->assertEquals($names, Post::cacheFor(now()->addHours(1))->pluck('name'));
        }));
    }

    public function test_pluck_with_key_is_cached_apart_from_pluck_without_key()
    {
        $posts = factory(Post::class, 3)->create();

        Post::cacheFor(now()->addHours(1))->pluck('name');
        $keyed = Post::cacheFor(now()->addHours(1))->pluck('name', 'id');

        $this->assertEquals($posts->pluck('name', 'id')->all(), $keyed->all());
        $this->assertSame(0, $this->countQueries(fn () => Post::cacheFor(now()->addHours(1))->pluck('name', 'id')));
    }

    public function test_pluck_is_not_cached_when_caching_is_off()
    {
        factory(Post::class, 3)->create();

        Post::dontCache()->pluck('name');

        $this->assertSame(1, $this->countQueries(fn () => Post::dontCache()->pluck('name')));
    }
}
