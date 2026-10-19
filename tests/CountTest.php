<?php

namespace Rennokki\QueryCache\Test;

use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use Rennokki\QueryCache\Test\Models\Post;

class CountTest extends TestCase
{
    #[DataProvider('strictModeContextProvider')]
    public function test_count(bool $strict)
    {
        $posts = factory(Post::class, 5)->create();
        $postsCount = Post::cacheFor(now()->addHours(1))->count();
        $cache = Cache::get('leqc:sqlitegetselect count(*) as '.$this->aggregateAlias().' from "posts"a:0:{}');

        $this->assertNotNull($cache);

        $this->assertEquals(
            $cache->first()->aggregate,
            $postsCount
        );
    }

    #[DataProvider('strictModeContextProvider')]
    public function test_count_with_columns(bool $strict)
    {
        $posts = factory(Post::class, 5)->create();
        $postsCount = Post::cacheFor(now()->addHours(1))->count('name');
        $cache = Cache::get('leqc:sqlitegetselect count("name") as '.$this->aggregateAlias().' from "posts"a:0:{}');

        $this->assertNotNull($cache);

        $this->assertEquals(
            $cache->first()->aggregate,
            $postsCount
        );
    }
}
