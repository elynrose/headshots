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
        $gen = new Generate();

        return view('frontend.generates.build', compact('generate', 'enabled_fals', 'childs', 'gen'));
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

        try {
            $final_response = $this->client->get($generate->response_url, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $final_result = json_decode($final_response->getBody(), true);

            $gen = new Generate();

            if (in_array($fal->model_type, ['image', 'prompt'])) {
                $generate->image_url = $final_result['images'][0]['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $res = [
                    "image_url" => $final_result['images'][0]['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                return response()->json($res);
            } elseif (in_array($fal->model_type, ['background'])) {
                $generate->image_url = $final_result['image']['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $res = [
                    "image_url" => $final_result['image']['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                return response()->json($res);

            } elseif (in_array($fal->model_type, ['video', 'upscale', 'audio'])) {
                $generate->video_url = $final_result['video']['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $res = [
                    "video_url" => $final_result['video']['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                return response()->json($res);
            }elseif (in_array($fal->model_type, ['train'])) {
                $generate->image_url = $final_result['images'][0]['url'];
                $generate->status = "COMPLETED";
                $generate->save();

                $res = [
                    "image_url" => $final_result['images'][0]['url'],
                    "type" => $fal->model_type,
                    "status" => "COMPLETED"
                ];

                return response()->json($res);
            }

            return $res;
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                $generate->status = "ERROR";
                $generate->save();
                \Log::error("Failed to get job status: " . $e->getMessage());
            }
            \Log::error("Failed to get job status: " . $e->getMessage());
        }
    }

    public function status(Request $request)
    {
        $generate = Generate::find($request->id);

        $client = new Client();

        try {
            $response = $client->post($generate->status_url, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (in_array($responseBody['status'], ["NEW", "IN_QUEUE", "PROCESSING"])) {
                $result = $this->getResults($generate);

                if (is_null($result)) {
                    return response()->json(['status' => 'IN_PROGRESS', 'type' => $generate->fal->model_type, 'queue_position'=>$responseBody['queue_position']], 200);
                }

                return $result;
            } elseif ($response->getStatusCode() == 200) {
                $generate->status = "COMPLETED";
                $generate->save();

                $result = $this->getResults($generate);

                return $result;
            }
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                $generate->status = "ERROR";
                $generate->save();
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            \Log::error("Failed to get job status: " . $e->getMessage());
            return response()->json(['error' => 'Failed to get job status'], 500);
        }
    }



    public function webhook()
    {
        $data = json_decode(file_get_contents(env('APP_URL').'/webhook'), true);
        \log::info('From the webhook'.$data);
        if (isset($data)) {
            $generate = Generate::where('requestid', $data['requestid'])->first();
            \Log::info('From the webhook'.$generate);
            dd($generate);
        }

    }
}
