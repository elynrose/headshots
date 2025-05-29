<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!Auth::user(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaigns = Campaign::with(['user', 'scenes'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(!Auth::user(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('frontend.campaigns.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(!Auth::user(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'title' => 'required|string|max:255',
            'prompt' => 'required|string'
        ]);

        $campaign = Campaign::create([
            'title' => $request->title,
            'prompt' => $request->prompt,
            'status' => 'NEW',
            'user_id' => Auth::id(),
        ]);

        // Trigger scene generation
        app(GenerateScenesController::class)->createScenes($campaign);

        return redirect()->route('frontend.campaigns.show', $campaign->id)
            ->with('success', 'Campaign created successfully. Scenes are being generated...');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        abort_if(!Auth::user() || $campaign->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaign->load(['user', 'scenes']);
        return view('frontend.campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        abort_if(!Auth::user() || $campaign->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('frontend.campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        abort_if(!Auth::user() || $campaign->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'title' => 'required|string|max:255',
            'prompt' => 'required|string'
        ]);

        $campaign->update($request->all());

        return redirect()->route('frontend.campaigns.show', $campaign->id)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        abort_if(!Auth::user() || $campaign->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaign->delete();

        return redirect()->route('frontend.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
