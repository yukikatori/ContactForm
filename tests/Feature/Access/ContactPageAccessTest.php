<?php

namespace Tests\Feature\Access;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactPageAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせフォーム入力ページが正常に表示され、categories・tagsがビュー変数として渡され、カテゴリ名・タグ名がページに表示される(): void
    {
        $category = Category::factory()->create(['content' => 'test1']);
        $tag = Tag::factory()->create(['name' => 'test2']);

        $response = $this->get('/');

        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
        $response->assertSee('test1');
        $response->assertSee('test2');
    }

    /** @test */
    public function サンクスページが正常に表示される(): void
    {
        $response = $this->get('/thanks');

        $response->assertStatus(200);
    }
}
