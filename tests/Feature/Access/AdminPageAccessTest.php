<?php

namespace Tests\Feature\Access;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;

class AdminPageAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証されたユーザーのみが管理ダッシュボードを表示でき、お問い合わせは新しい順で表示される(): void
    {
        $category = Category::factory()->create();

        $newContact = Contact::factory()->create([
            'category_id' => $category->id,
            'created_at' => now(),
        ]);

        $oldContact = Contact::factory()->create([
            'category_id' => $category->id,
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)->get('/admin');

        $response->assertSeeInOrder([
            $newContact->first_name,
            $oldContact->first_name,
        ]);
    }

    /** @test */
    public function 未認証ユーザーはログイン画面にリダイレクトされる(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}
