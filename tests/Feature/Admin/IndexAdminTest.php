<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;

class IndexAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function 管理者画面にアクセスでき、お問い合わせが7件ごとにページネーションされ、各お問い合わせにタグが紐づく(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $contacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
        ]);

        foreach($contacts as $contact) {
            $contact->tags()->attach($tag->id);
        }

        $response = $this->actingAs($this->user)->get('/admin');

        $response->assertViewHas('contacts', function ($viewContacts) {
            return $viewContacts->count() === 7;
        });

        $response->assertSee('page=2');

        $response->assertViewHas('contacts', function ($viewContacts) {
            return $viewContacts->every(fn ($contact) => $contact->tags->isNotEmpty());
        });
    }

    /** @test */
    public function キーワード検索実施後ページネーションが機能する(): void
    {
        $category = Category::factory()->create();

        $targetContacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'first_name' => 'test',
        ]);

        $otherContacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'first_name' => 'fail',
        ]);

        $response = $this->actingAs($this->user)->get('/admin?keyword=test');

        $response->assertViewHas('contacts', function ($viewContacts) {
            return $viewContacts->count() === 7
                && $viewContacts->every(fn ($c) => $c->first_name === 'test');
        });

        $response->assertSee('page=2');

        $response->assertDontSee('fail');
    }

    /** @test */
    public function 性別検索実施後ページネーションが機能する(): void
    {
        $category = Category::factory()->create();

        $targetContacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'first_name' => 'Man',
            'gender' => 1,
        ]);

        $otherContacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'first_name' => 'Woman',
            'gender' => 2,
        ]);

        $response = $this->actingAs($this->user)->get('/admin?gender=1');

        $response->assertViewHas('contacts', function ($viewContacts) {
            return $viewContacts->count() === 7
                && $viewContacts->every(fn($contact) => $contact->gender === 1);
        });

        $response->assertSee('page=2');

        $response->assertDontSee('Woman');
    }

    /** @test */
    public function カテゴリフィルタ実施後ページネーションが機能する(): void
    {
        $targetCategory = Category::factory()->create(['content' => 'test']);
        $otherCategory = Category::factory()->create(['content' => 'other']);

        $targetContacts = Contact::factory()->count(10)->create([
            'category_id' => $targetCategory->id,
            'first_name' => 'test',
        ]);

        $otherContacts = Contact::factory()->count(10)->create([
            'category_id' => $otherCategory->id,
            'first_name' => 'fail',
        ]);

        $response = $this->actingAs($this->user)->get('/admin?category_id=' . $targetCategory->id);

        $response->assertViewHas('contacts', function ($viewContacts) use ($targetCategory) {
            return $viewContacts->count() === 7
                && $viewContacts->every(fn($contact) => $contact->category_id === $targetCategory->id);
        });

        $response->assertSee('page=2');

        $response->assertDontSee('fail');
    }

    /** @test */
    public function 日付フィルタ実施後ページネーションが機能する(): void
    {
        $category = Category::factory()->create();

        $targetContacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'created_at' => '2026-07-21',
        ]);

        $otherContacts = Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'created_at' => '2026-07-20',
        ]);

        $response = $this->actingAs($this->user)->get('/admin?date=2026-07-21');

        $response->assertViewHas('contacts', function ($viewContacts) {
            return $viewContacts->count() === 7
                && $viewContacts->every(fn ($contact) => $contact->created_at->toDateString() === '2026-07-21');
        });

        $response->assertSee('page=2');
    }
}
