<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\Tag;


class TagController extends Controller
{
    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Tag::create($validated);

        return redirect()->route('admin.index');
    }

    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validated();

        $tag->update($validated);

        return redirect()->route('admin.index');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('admin.index'); 
    }
}
