<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyGenerateRequest;
use App\Http\Requests\StoreGenerateRequest;
use App\Http\Requests\UpdateGenerateRequest;
use App\Models\Generate;
use App\Models\Train;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Exception;
use App\Models\Fal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Events\GenerateStatusUpdated;
use Illuminate\Support\Facades\Http;

class GenerateController extends Controller
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('FAL_AI_API_KEY');
    }

    public function index()
    {
        abort_if(Gate::denies('generate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $generates = Generate::with(['train', 'user'])
            ->where('parent', null)
            ->where('user_id', Auth::id())
            ->orderBy('id', 'desc')->paginate(9);

        $fals = Fal::get();

        return view('frontend.generates.index', compact('generates', 'fals'));
    }

    public function build(Request $request)
    {
        abort_if(Gate::denies('generate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $fal = Fal::where('id', $request->model_id)->first();

        if (!$fal) {
            abort(404, 'Payload template not found.');
        }

        if ($fal->model_type === 'train') {
            $generate = Generate::with(['train', 'user'])
                ->where('id', $request->generate_id)
                ->where('parent', null)
                ->first();
        } else {
            $generate = Generate::with(['user'])
                ->where('id', $request->generate_id)
                ->where('parent', null)
                ->first();
        }

        if ($fal->model_type === 'train') {
            $childs = Generate::with(['train', 'user'])
                ->where('parent', $request->generate_id)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $childs = Generate::with(['user'])
                ->where('parent', $request->generate_id)
                ->orderBy('id', 'asc')
                ->get();
        }

        $enabled_fals = Fal::where('enabled', true)->get();
        $fal = Fal::where('id', $request->model_id)->first();
        $gen = new Generate();

        return view('frontend.generates.build', compact('generate', 'enabled_fals', 'childs', 'gen', 'fal'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('generate_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trains = Train::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        $fals = Fal::find(request('model_id'));
        $modelData = Fal::where('id', $request->model_id)->first();

        if (!$modelData) {
            abort(404, 'Payload template not found.');
        }

        $payloadDetails = json_decode($modelData->payload, true);

        if ($request->image_id) {
            $existingImages = Generate::where('id', $request->image_id)->first();
        } else {
            $existingImages = null;
        }

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.generates.create', compact('trains', 'users', 'fals', 'existingImages', 'payloadDetails', 'modelData'));
    }

    public function store(StoreGenerateRequest $request)
    {
        if ($request->file('audio_url')) {
            $audio_file = $request->file('audio_url');
            $audio_file_name = time() . '.' . $audio_file->getClientOriginalExtension();
            $audio_file_path = 'audio/' . $audio_file_name;
            \Storage::disk('s3')->put($audio_file_path, file_get_contents($audio_file), 'public');
            $audio_url = \Storage::disk('s3')->url($audio_file_path);
        }

        if ($request->file('photo')) {
            $photo_file = $request->file('photo');
            $photo_file_name = time() . '.' . $photo_file->getClientOriginalExtension();
            $photo_file_path = 'photos/' . $photo_file_name;
            \Storage::disk('s3')->put($photo_file_path, file_get_contents($photo_file), 'public');
            $image_url = \Storage::disk('s3')->url($photo_file_path);
        } else {
            $image_url = $request->image_url ?? null;
        }

        $generate = Generate::create([
            'prompt' => $request->prompt ?? null,
            'fal_model_id' => $request->fal_model_id ?? null,
            'train_id' => $request->train_id,
            'width' => $request->width ?? null,
            'height' => $request->height ?? null,
            'status' => 'NEW',
            'content_type' => $request->content_type ?? null,
            'response_url' => $request->response_url,
            'status_url' => $request->status_url,
            'cancel_url' => $request->cancel_url,
            'queue_position' => $request->queue_position ?? null,
            'requestid' => $request->requestid ?? null,
            'image_url' => $image_url ?? null,
            'video_url' => $request->video_url ?? null,
            'audio_url' => $audio_url ?? null,
            'parent' => $request->parent ?? null,
            'inference' => $request->inference ?? null,
            'user_id' => Auth::id(),
            'seed' => $request->seed ?? null,
            'credit' => $request->credit ?? null,
        ]);

        if (in_array($request->content_type, ['image', 'prompt', 'train'])) {
            $generates = Generate::where('user_id', Auth::id())->get();
            return redirect()->route('frontend.generates.index', compact('generates'));
        } else {
            return redirect()->route('frontend.generates.build', ['generate_id' => $request->parent, 'model_id' => $request->fal_model_id]);
        }
    }

    public function edit(Generate $generate)
    {
        abort_if(Gate::denies('generate_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trains = Train::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $generate->load('train', 'user');

        return view('frontend.generates.edit', compact('generate', 'trains', 'users'));
    }

    public function update(UpdateGenerateRequest $request, Generate $generate)
    {
        $generate->update($request->all());

        return redirect()->route('frontend.generates.index');
    }

    public function show(Generate $generate)
    {
        abort_if(Gate::denies('generate_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $generate->load('train', 'user');

        return view('frontend.generates.show', compact('generate'));
    }

    public function destroy(Generate $generate)
    {
        abort_if(Gate::denies('generate_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $generate->delete();

        return back();
    }

    public function massDestroy(MassDestroyGenerateRequest $request)
    {
        $generates = Generate::find(request('ids'));

        foreach ($generates as $generate) {
            $generate->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getResults($generate)
    {
        $fal = Fal::where('id', $generate->fal_model_id)->first();
        \Log::info('Getting results for generate ID: ' . $generate->id, [
            'fal_model' => $fal,
            'response_url' => $generate->response_url
        ]);

        try {
            $final_response = $this->client->get($generate->response_url, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $final_result = json_decode($final_response->getBody(), true);
            \Log::info('Final API response:', $final_result);

            $gen = new Generate();

            if (in_array($fal->model_type, ['image', 'prompt'])) {
                $generate->image_url = $final_result['images'][0]['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $this->deductCredits($fal->model_type);

                $res = [
                    "image_url" => $final_result['images'][0]['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                \Log::info('Returning image result:', $res);
                return response()->json($res);

            } elseif (in_array($fal->model_type, ['background'])) {
                $generate->image_url = $final_result['image']['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $this->deductCredits($fal->model_type);

                $res = [
                    "image_url" => $final_result['image']['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                \Log::info('Returning background result:', $res);
                return response()->json($res);
            } elseif (in_array($fal->model_type, ['video', 'audio'])) {
                $generate->video_url = $final_result['video']['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $this->deductCredits($fal->model_type);

                $res = [
                    "video_url" => $final_result['video']['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                \Log::info('Returning video/audio result:', $res);
                return response()->json($res);
            } elseif (in_array($fal->model_type, ['train'])) {
                $generate->image_url = $final_result['images'][0]['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $this->deductCredits($fal->model_type);

                $res = [
                    "image_url" => $final_result['images'][0]['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                \Log::info('Returning train result:', $res);
                return response()->json($res);
            }

            return response()->json(['error' => 'Unknown model type'], 400);
        } catch (Exception $e) {
            \Log::error('Error getting results for generate ID: ' . $generate->id, [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            if ($e->getCode() == 401) {
                $generate->status = "ERROR";
                $generate->save();
            }
            throw $e;
        }
    }

    public function status(Request $request)
    {
        try {
            $generate = Generate::findOrFail($request->id);
            
            if ($generate->status_url) {
                $response = Http::withHeaders([
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->get($generate->status_url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    \Log::info('Fal.ai status response:', $data);
                    
                    // Update status and queue position
                    $generate->status = $data['status'] ?? $generate->status;
                    $generate->queue_position = $data['queue_position'] ?? null;
                    
                    // If completed, fetch the result from response_url
                    if ($data['status'] === 'COMPLETED' && $data['response_url']) {
                        \Log::info('Fetching result from response URL:', ['url' => $data['response_url']]);
                        
                        $resultResponse = Http::withHeaders([
                            'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                            'Content-Type' => 'application/json',
                        ])->get($data['response_url']);
                        
                        if ($resultResponse->successful()) {
                            $resultData = $resultResponse->json();
                            \Log::info('Fal.ai result response:', $resultData);
                            
                            $fal = Fal::where('id', $generate->fal_model_id)->first();
                            
                            if ($fal) {
                                if (in_array($fal->model_type, ['video', 'audio'])) {
                                    if (isset($resultData['video']) && isset($resultData['video']['url'])) {
                                        $generate->video_url = $resultData['video']['url'];
                                        \Log::info('Updated video URL from result:', [
                                            'url' => $resultData['video']['url'],
                                            'content_type' => $resultData['video']['content_type'] ?? null,
                                            'file_name' => $resultData['video']['file_name'] ?? null
                                        ]);
                                    } else {
                                        \Log::warning('Video URL not found in result response for video/audio type');
                                    }
                                } elseif (in_array($fal->model_type, ['image', 'prompt', 'train'])) {
                                    if (isset($resultData['images']) && !empty($resultData['images'])) {
                                        $generate->image_url = $resultData['images'][0]['url'];
                                        \Log::info('Updated image URL:', ['url' => $resultData['images'][0]['url']]);
                                    }
                                } elseif (in_array($fal->model_type, ['background'])) {
                                    if (isset($resultData['image']) && isset($resultData['image']['url'])) {
                                        $generate->image_url = $resultData['image']['url'];
                                        \Log::info('Updated background image URL:', ['url' => $resultData['image']['url']]);
                                    }
                                }
                            }
                        } else {
                            \Log::error('Failed to fetch result from response URL:', [
                                'status' => $resultResponse->status(),
                                'body' => $resultResponse->body()
                            ]);
                        }
                    }
                    
                    $generate->save();
                    \Log::info('Updated generate record:', [
                        'id' => $generate->id,
                        'status' => $generate->status,
                        'image_url' => $generate->image_url,
                        'video_url' => $generate->video_url
                    ]);

                    // Broadcast the status update
                    event(new GenerateStatusUpdated($generate));

                    // Return complete response with media URLs
                    return response()->json([
                        'status' => $generate->status,
                        'queue_position' => $generate->queue_position,
                        'type' => $generate->content_type,
                        'image_url' => $generate->image_url,
                        'video_url' => $generate->video_url
                    ]);
                }
            }

            // If no status_url or request failed, return current state
            return response()->json([
                'status' => $generate->status,
                'queue_position' => $generate->queue_position,
                'type' => $generate->content_type,
                'image_url' => $generate->image_url,
                'video_url' => $generate->video_url
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking status: ' . $e->getMessage());
            return response()->json(['error' => 'Error checking status: ' . $e->getMessage()], 500);
        }
    }

    public function webhook()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        \Log::info('Webhook received:', $data);
        
        if (isset($data['requestid'])) {
            $generate = Generate::where('requestid', $data['requestid'])->first();
            
            if ($generate) {
                \Log::info('Found generate record:', ['generate' => $generate]);
                
                // Update status and queue position
                $generate->status = $data['status'] ?? $generate->status;
                $generate->queue_position = $data['queue_position'] ?? null;
                
                // If completed, update URLs
                if ($data['status'] === 'COMPLETED') {
                    \Log::info('Processing completed status for generate ID: ' . $generate->id);
                    
                    // Get the model type
                    $fal = Fal::where('id', $generate->fal_model_id)->first();
                    if ($fal) {
                        \Log::info('Model type:', ['type' => $fal->model_type]);
                        
                        if (in_array($fal->model_type, ['video', 'audio'])) {
                            if (isset($data['video']) && isset($data['video']['url'])) {
                                $generate->video_url = $data['video']['url'];
                                \Log::info('Updated video URL from webhook:', [
                                    'url' => $data['video']['url'],
                                    'content_type' => $data['video']['content_type'] ?? null,
                                    'file_name' => $data['video']['file_name'] ?? null
                                ]);
                            } else {
                                \Log::warning('Video URL not found in webhook data for video/audio type');
                            }
                        } elseif (in_array($fal->model_type, ['image', 'prompt', 'train'])) {
                            if (isset($data['images']) && !empty($data['images'])) {
                                $generate->image_url = $data['images'][0]['url'];
                                \Log::info('Updated image URL:', ['url' => $data['images'][0]['url']]);
                            } else {
                                \Log::warning('Image URL not found in webhook data for image type');
                            }
                        } elseif (in_array($fal->model_type, ['background'])) {
                            if (isset($data['image']) && isset($data['image']['url'])) {
                                $generate->image_url = $data['image']['url'];
                                \Log::info('Updated background image URL:', ['url' => $data['image']['url']]);
                            } else {
                                \Log::warning('Image URL not found in webhook data for background type');
                            }
                        }
                    } else {
                        \Log::warning('Fal model not found for generate ID: ' . $generate->id);
                    }
                }
                
                $generate->save();
                \Log::info('Updated generate record:', [
                    'id' => $generate->id,
                    'status' => $generate->status,
                    'image_url' => $generate->image_url,
                    'video_url' => $generate->video_url
                ]);
                
                // Broadcast the status update
                event(new GenerateStatusUpdated($generate));
                
                \Log::info('Webhook processed successfully');
                return response()->json(['status' => 'success']);
            }
            
            \Log::warning('Generate record not found for requestid:', ['requestid' => $data['requestid']]);
            return response()->json(['status' => 'error', 'message' => 'Generate record not found'], 404);
        }
        
        \Log::warning('Invalid webhook data received');
        return response()->json(['status' => 'error', 'message' => 'Invalid webhook data'], 400);
    }

    public function deductCredits($type){
        //Check users credit balance

        $credit = Credit::where('email', Auth::user()->email)->first();
        if (!$credit) {
            return false;
        }
        if ($credit->points < env($type)) {
            return false;
        }
        //Deduct credits
        $credit = $credit->points - env($type);
        $credit->save();
    }
}

