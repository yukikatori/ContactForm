<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;

class DeleteContactTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function レコードが正常に削除され、管理画面にリダイレクトされる(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact->tags()->attach($tag->id);

        $response = $this->actingAs($this->user)->delete('/admin/contacts/' . $contact->id);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        $this->assertDatabaseMissing('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);

        $response->assertRedirect('/admin');
    }
}
