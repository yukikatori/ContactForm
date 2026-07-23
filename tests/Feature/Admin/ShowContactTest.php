<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;

class ShowContactTest extends TestCase
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
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->get('/admin/contacts/' . $contact->id);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function 指定したお問い合わせがカテゴリ、タグ付きで詳細ページが表示される(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $contact = Contact::factory()->create([
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada.taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1-1',
            'building' => '渋谷ビル301',
            'detail' => '商品の発送について確認したいです',
            'category_id' => $category->id,
        ]);

        $contact->tags()->attach($tag->id);

        $response = $this->actingAs($this->user)->get('/admin/contacts/' . $contact->id);

        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('男性');
        $response->assertSee('yamada.taro@example.com');
        $response->assertSee('09012345678');
        $response->assertSee('東京都渋谷区1-1-1');
        $response->assertSee('渋谷ビル301');
        $response->assertSee('商品の発送について確認したいです');
        $response->assertSee($category->content);
        $response->assertSee($tag->name);
    }
}
