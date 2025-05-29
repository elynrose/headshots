<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Scene;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SceneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!Auth::user(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $scenes = Scene::with(['campaign', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.scenes.index', compact('scenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(!Auth::user(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaigns = Campaign::where('user_id', Auth::id())->get();
        return view('frontend.scenes.create', compact('campaigns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(!Auth::user(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|string|max:10',
            'character_description' => 'required|string',
            'background_description' => 'required|string',
            'character_actions' => 'required|string',
            'voice_over' => 'nullable|string',
            'camera_angle' => 'nullable|string|max:50',
            'zoom_level' => 'nullable|string|max:50',
            'status' => 'required|string|max:20',
            'campaign_id' => 'required|exists:campaigns,id'
        ]);

        Scene::create([
            'title' => $request->title,
            'language' => $request->language,
            'character_description' => $request->character_description,
            'background_description' => $request->background_description,
            'character_actions' => $request->character_actions,
            'voice_over' => $request->voice_over,
            'camera_angle' => $request->camera_angle,
            'zoom_level' => $request->zoom_level,
            'status' => $request->status,
            'campaign_id' => $request->campaign_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('frontend.scenes.index')
            ->with('success', 'Scene created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Scene $scene)
    {
        abort_if(!Auth::user() || $scene->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $scene->load(['campaign', 'user']);

        return view('frontend.scenes.show', compact('scene'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scene $scene)
    {
        abort_if(!Auth::user() || $scene->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaigns = Campaign::where('user_id', Auth::id())->get();
        return view('frontend.scenes.edit', compact('scene', 'campaigns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scene $scene)
    {
        abort_if(!Auth::user() || $scene->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|string|max:10',
            'character_description' => 'required|string',
            'background_description' => 'required|string',
            'character_actions' => 'required|string',
            'voice_over' => 'nullable|string',
            'camera_angle' => 'nullable|string|max:50',
            'zoom_level' => 'nullable|string|max:50',
            'status' => 'required|string|max:20',
            'campaign_id' => 'required|exists:campaigns,id'
        ]);

        $scene->update($request->all());

        return redirect()->route('frontend.scenes.index')
            ->with('success', 'Scene updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scene $scene)
    {
        abort_if(!Auth::user() || $scene->user_id !== Auth::id(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $scene->delete();

        return redirect()->route('frontend.scenes.index')
            ->with('success', 'Scene deleted successfully.');
    }
}
