<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = Contact::factory()->count(20)->create();

        foreach ($contacts as $contact) {
            $selectedTags = Tag::all()->random(rand(1, 3));
            $contact->tags()->attach($selectedTags->pluck('id'));
        }
    }
}
