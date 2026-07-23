<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function １つのカテゴリが複数のお問い合わせに紐づく(): void
    {
        $category = Category::factory()->create();
        $contacts = Contact::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->contacts);
        $this->assertInstanceOf(Contact::class, $category->contacts->first());
    }

    /** @test */
    public function １つのお問い合わせが特定のカテゴリに属する(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'category_id' => $category->id,
        ]);

        $this->assertEquals($category->id, $contact->category->id);
        $this->assertInstanceOf(Category::class, $contact->category);
    }

    /** @test */
    public function 中間テーブルを介して、1つの問い合わせが複数のタグに紐づいている(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);
        $tags = Tag::factory()->count(3)->create();

        $contact->tags()->attach($tags->pluck('id'));

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('contact_tag', [
                'contact_id' => $contact->id,
                'tag_id' => $tag->id,
            ]);
        }

        $this->assertCount(3, $contact->tags);
        $this->assertInstanceOf(Tag::class, $contact->tags->first());
    }

    /** @test */
    public function 中間テーブルを介して、1つのタグが複数の問い合わせに紐づいている(): void
    {
        $category = Category::factory()->create();
        $contacts = Contact::factory()->count(3)->create(['category_id' => $category->id]);
        $tag = Tag::factory()->create();

        foreach ($contacts as $contact) {
            $contact->tags()->attach($tag->id);
        }

        foreach ($contacts as $contact) {
            $this->assertDatabaseHas('contact_tag', [
                'contact_id' => $contact->id,
                'tag_id' => $tag->id,
            ]);
        }

        $this->assertCount(3, $tag->contacts);
        $this->assertInstanceOf(Contact::class, $tag->contacts->first());
    }
}
