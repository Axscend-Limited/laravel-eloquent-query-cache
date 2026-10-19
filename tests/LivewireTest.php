<?php

namespace Rennokki\QueryCache\Test;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Rennokki\QueryCache\Test\Models\Post;

class LivewireTest extends TestCase
{
    #[DataProvider('strictModeContextProvider')]
    public function test_livewire_component_poll_doesnt_break_when_callback_is_already_set(bool $strict)
    {
        // See: https://github.com/renoki-co/laravel-eloquent-query-cache/issues/163
        // Registered under getName() so the refresh request can resolve it.
        Livewire::component('post', PostComponent::class);

        $posts = factory(Post::class, 30)->create();

        Livewire::test(PostComponent::class, ['post' => $posts->first()])
            ->assertOk()
            ->assertSee($posts[0]->name)
            ->call('$refresh')
            ->assertOk()
            ->assertSee($posts[0]->name);
    }
}

class PostComponent extends Component
{
    public Post $post;

    public function getName()
    {
        return 'post';
    }
}
