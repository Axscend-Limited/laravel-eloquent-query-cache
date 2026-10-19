<?php

namespace Rennokki\QueryCache\Test;

use Rennokki\QueryCache\Test\Models\Post;

class ExistsTest extends TestCase
{
    public function test_exists_is_cached()
    {
        $post = factory(Post::class)->create();

        $this->assertTrue(Post::cacheFor(now()->addHours(1))->whereKey($post->id)->exists());

        $this->assertSame(0, $this->countQueries(function () use ($post) {
            $this->assertTrue(Post::cacheFor(now()->addHours(1))->whereKey($post->id)->exists());
        }));
    }

    public function test_false_exists_result_is_cached()
    {
        $this->assertTrue(Post::cacheFor(now()->addHours(1))->whereKey(999)->doesntExist());

        $this->assertSame(0, $this->countQueries(function () {
            $this->assertTrue(Post::cacheFor(now()->addHours(1))->whereKey(999)->doesntExist());
        }));
    }

    public function test_exists_is_cached_apart_from_get()
    {
        $post = factory(Post::class)->create();

        Post::cacheFor(now()->addHours(1))->whereKey($post->id)->get();

        $this->assertTrue(Post::cacheFor(now()->addHours(1))->whereKey($post->id)->exists());
    }

    public function test_exists_is_not_cached_when_caching_is_off()
    {
        Post::dontCache()->exists();

        $this->assertSame(1, $this->countQueries(fn () => Post::dontCache()->exists()));
    }
}
