<?php

namespace Tests\Feature\Contact;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Tag;

class ConfirmContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ作成でバリデーション通過時にお問い合わせフォーム確認ページが表示され、入力内容が画面に表示される(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada.taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1-1',
            'building' => '渋谷ビル301',
            'detail' => '商品の発送について確認したいです',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts/confirm', $data);

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

    /** 
     * @test 
     * @dataProvider invalidDataProvider
     */
    public function バリデーションエラー時はリダイレクトされエラーが返る($inputField, $value, $errorField, $reason): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $validData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada.taro@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1-1',
            'building' => '渋谷ビル301',
            'detail' => '商品の発送について確認したいです',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
        ];

        $invalidData = array_merge($validData, [$inputField => $value]);

        $response = $this->post('/contacts/confirm', $invalidData);

        $response->assertSessionHasErrors([$errorField]);
    }

    public static function invalidDataProvider(): array
    {
        return [
            // --- first_name ---
            ['first_name', '', 'first_name', '必須項目'],
            ['first_name', 123, 'first_name', '文字列である必要がある'],
            ['first_name', str_repeat('あ', 256), 'first_name', '255文字以内'],

            // --- last_name ---
            ['last_name', '', 'last_name', '必須項目'],
            ['last_name', 123, 'last_name', '文字列である必要がある'],
            ['last_name', str_repeat('あ', 256), 'last_name', '255文字以内'],

            // --- gender ---
            ['gender', '', 'gender', '必須項目'],
            ['gender', 'abc', 'gender', '整数である必要がある'],
            ['gender', 999, 'gender', '1,2,3 のいずれか'],
            ['gender', 0, 'gender', '1,2,3 のいずれか'],

            // --- email ---
            ['email', '', 'email', '必須項目'],
            ['email', 'not-email', 'email', 'メール形式'],
            ['email', str_repeat('a', 256) . '@example.com', 'email', '255文字以内'],

            // --- tel ---
            ['tel', '', 'tel', '必須項目'],
            ['tel', 'abc', 'tel', '数字のみ'],
            ['tel', '123', 'tel', '10〜11桁の数字'],
            ['tel', '090123456789', 'tel', '11桁以内'],

            // --- address ---
            ['address', '', 'address', '必須項目'],
            ['address', 123, 'address', '文字列である必要がある'],
            ['address', str_repeat('あ', 256), 'address', '255文字以内'],

            // --- building ---
            ['building', 123, 'building', '文字列である必要がある'],
            ['building', str_repeat('あ', 256), 'building', '255文字以内'],

            // --- detail ---
            ['detail', '', 'detail', '必須項目'],
            ['detail', 123, 'detail', '文字列である必要がある'],
            ['detail', str_repeat('あ', 121), 'detail', '120文字以内'],

            // --- category_id ---
            ['category_id', '', 'category_id', '必須項目'],
            ['category_id', 'abc', 'category_id', '整数である必要がある'],
            ['category_id', 999, 'category_id', '存在しないID'],

            // --- tag_ids（配列でない）---
            ['tag_ids', 'abc', 'tag_ids', '配列である必要がある'],

            // --- tag_ids.*（中身が不正）---
            ['tag_ids', [999], 'tag_ids.0', '存在しないタグID'],
            ['tag_ids', ['abc'], 'tag_ids.0', '整数である必要がある'],
        ];
    }
}
