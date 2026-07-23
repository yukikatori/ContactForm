<?php

namespace Tests\Feature\Tag;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tag;
use App\Models\User;

class UpdateTagTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function 未認証ユーザーはloginにリダイレクトされる(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get('/admin/tags/' . $tag->id . '/edit');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 編集画面に既存のタグ名が表示される(): void
    {
        $tag = Tag::factory()->create(['name' => 'test']);

        $response = $this->actingAs($this->user)->get('/admin/tags/' . $tag->id . '/edit');

        $response->assertSee('test');
    }
}
