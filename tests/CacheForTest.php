<?php

namespace Rennokki\QueryCache\Test;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Test\Models\Post;

class CacheForTest extends TestCase
{
    public function test_reused_builder_uses_cache_after_a_miss()
    {
        factory(Post::class)->create();

        $query = Post::cacheFor(now()->addHours(1));
        $query->get();

        $this->assertSame(0, $this->countQueries(fn () => $query->get()));
    }

    public function test_cache_for_accepts_carbon_immutable()
    {
        factory(Post::class)->create();

        Post::cacheFor(CarbonImmutable::now()->addHours(1))->get();

        $this->assertNotNull(Cache::get('leqc:sqlitegetselect * from "posts"a:0:{}'));
    }

    public function test_cache_for_zero_does_not_cache()
    {
        factory(Post::class)->create();

        Post::cacheFor(0)->get();

        $this->assertNull(Cache::get('leqc:sqlitegetselect * from "posts"a:0:{}'));
    }

    public function test_cache_forever_still_caches()
    {
        factory(Post::class)->create();

        Post::cacheForever()->get();

        $this->assertNotNull(Cache::get('leqc:sqlitegetselect * from "posts"a:0:{}'));
    }
}
