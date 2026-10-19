<?php

namespace Rennokki\QueryCache\Test;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\DataProvider;
use Rennokki\QueryCache\Test\Models\Page;

class FlushCacheOnUpdateTest extends TestCase
{
    #[DataProvider('strictModeContextProvider')]
    public function test_flush_cache_on_create(bool $strict)
    {
        $page = factory(Page::class)->create();
        $storedPage = Page::cacheFor(now()->addHours(1))->first();
        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNotNull($cache);

        $this->assertEquals(
            $cache->first()->id,
            $storedPage->id
        );

        Page::create([
            'name' => '9GAG',
        ]);

        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNull($cache);
    }

    #[DataProvider('strictModeContextProvider')]
    public function test_flush_cache_on_update(bool $strict)
    {
        $page = factory(Page::class)->create();
        $storedPage = Page::cacheFor(now()->addHours(1))->first();
        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNotNull($cache);

        $this->assertEquals(
            $cache->first()->id,
            $storedPage->id
        );

        $page->update([
            'name' => '9GAG',
        ]);

        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNull($cache);
    }

    #[DataProvider('strictModeContextProvider')]
    public function test_flush_cache_on_delete(bool $strict)
    {
        $page = factory(Page::class)->create();
        $storedPage = Page::cacheFor(now()->addHours(1))->first();
        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNotNull($cache);

        $this->assertEquals(
            $cache->first()->id,
            $storedPage->id
        );

        $page->delete();

        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNull($cache);
    }

    #[DataProvider('strictModeContextProvider')]
    public function test_flush_cache_on_force_deletion(bool $strict)
    {
        $page = factory(Page::class)->create();
        $storedPage = Page::cacheFor(now()->addHours(1))->first();
        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNotNull($cache);

        $this->assertEquals(
            $cache->first()->id,
            $storedPage->id
        );

        $page->forceDelete();

        $cache = $this->getCacheWithTags('leqc:sqlitegetselect * from "pages" limit 1a:0:{}', ['test']);

        $this->assertNull($cache);
    }

    public function test_model_with_flush_observer_boots_and_reboots()
    {
        foreach ([1, 2] as $boot) {
            // Listeners outlive clearBootedModels(); flush them first, as flushing boots the model.
            Page::flushEventListeners();
            Model::clearBootedModels();

            factory(Page::class)->create();

            $this->assertTrue(
                Page::getEventDispatcher()->hasListeners('eloquent.created: '.Page::class),
                "Flush observer missing after boot {$boot}."
            );
        }
    }
}
