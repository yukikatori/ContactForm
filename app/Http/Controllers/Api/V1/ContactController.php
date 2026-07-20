<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Api\V1\ContactResource;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Contact::with(['category', 'tags'])
            ->filter($validated)
            ->latest();

        $perPage = $validated['per_page'] ?? 20;

        $contacts = $query->paginate($perPage);

        return response()->json([
            'data' => ContactResource::collection($contacts),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
            ],
            'links' => [
                'first' => $contacts->url(1),
                'last' => $contacts->url($contacts->lastPage()),
                'prev' => $contacts->previousPageUrl(),
                'next' => $contacts->nextPageUrl(),
            ],
        ], 200);
    }

    public function show(Contact $contact): JsonResponse
    {
        $contact->load(['category', 'tags']);

        return response()->json([
            'data' => new ContactResource($contact),
        ], 200);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $contact = Contact::create($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);

        return response()->json([
            'data' => new ContactResource($contact),
        ], 201);
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $validated = $request->validated();

        $contact->update($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);

        return response()->json([
            'data' => new ContactResource($contact),
        ], 200);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}
