<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Votes;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $websites = Websites::with('categories', 'votes')
            ->withCount('votes')
            ->when($request->search, function($query) use ($request) {
                $query->where('name', 'like', "%$request->search%");
            })
            ->orderBy('votes_count', 'desc')
            ->get();

        return response()->json($websites);
    }

    // Show a single website by ID
    public function show($id)
    {
        $website = Websites::with('categories', 'votes')->findOrFail($id);
        return response()->json($website);
    }

    // Create a new website
    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'category_ids' => 'array|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $website = Websites::create([
            'name' => $request->name,
            'url' => $request->url,
            'description' => $request->description,
            'added_by' => $user->id,
        ]);

        if ($website && $request->category_ids) {
            $website->categories()->attach($request->category_ids);
        }

        return response()->json($website, 201);
    }

    // Update an existing website
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|url',
            'description' => 'nullable|string',
            'category_ids' => 'array|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $website = Websites::findOrFail($id);

        $website->update($request->only(['name', 'url', 'description']));

        if ($request->category_ids) {
            $website->categories()->sync($request->category_ids);
        }

        return response()->json($website);
    }

    // Delete a website
    public function destroy($id)
    {
        $website = Websites::findOrFail($id);
        $website->delete();

        return response()->json(['message' => 'Website deleted successfully']);
    }

    // Vote for a website
    public function vote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vote' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $website = Websites::findOrFail($id);

        // Check if the user has already voted
        $existingVote = Votes::where('user_id', $user->id)
            ->where('website_id', $website->id)
            ->first();

        if ($existingVote) {
            $existingVote->update(['vote' => $request->vote]);
        } else {
            Votes::create([
                'user_id' => $user->id,
                'website_id' => $website->id,
                'vote' => $request->vote,
            ]);
        }

        return response()->json(['message' => 'Vote recorded']);
    }
}
