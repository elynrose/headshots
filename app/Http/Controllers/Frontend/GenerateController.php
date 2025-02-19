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
use App\Models\Photo;
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

        $generates = Generate::with(['train', 'user'])
        ->where('id', $request->generate_id)
        ->where('parent', null)
        ->get();

        $childs = Generate::with(['train', 'user'])
        ->where('parent', $request->generate_id)
        ->orderBy('id', 'desc')
        ->get();


        $fals = Fal::get();

        return view('frontend.generates.build', compact('generates', 'fals', 'childs'));
    }


    public function create(Request $request)
    {
        abort_if(Gate::denies('generate_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trains = Train::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        $fals = Fal::find(request('model_id'));
        
        if($request->image_id){
        $existingImages = Generate::where('id', $request->image_id)->first();
        }  else {
            $existingImages = null;
        }

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        return view('frontend.generates.create', compact('trains', 'users', 'fals', 'existingImages'));
    }

    public function store(StoreGenerateRequest $request)
    {
        //get the audio file
       /* if($request->has('audio_mp3')){
            $audio_blob = $request->input('audio_mp3');
            $audio_file_name = time() . '.mp3';
            $audio_file_path = 'audio/' . $audio_file_name;
            \Storage::disk('s3')->put($audio_file_path, base64_decode($audio_blob), 'public');
            $audio_url = \Storage::disk('s3')->url($audio_file_path);
        } else {
            $audio_file_path = null;
        }*/

        if($request->file('audio_mp3')){
            //save to s3 storage and get url
            $audio_file = $request->file('audio_mp3');
            $audio_file_name = time() . '.' . $audio_file->getClientOriginalExtension();
            $audio_file_path = 'audio/' . $audio_file_name;
            \Storage::disk('s3')->put($audio_file_path, file_get_contents($audio_file), 'public');
            $audio_url = \Storage::disk('s3')->url($audio_file_path);

        }

        $generate = Generate::create(
            [
                'prompt' => $request->prompt ?? null,
                'fal_model_id' => $request->fal_model_id,
                'train_id' => $request->train_id,
                'width' => $request->width ?? null,
                'height' => $request->height ?? null,
                'status' => 'NEW',
                'content_type' => 'image' ?? null,
                'response_url' => $request->response_url,
                'status_url' => $request->status_url,
                'cancel_url' => $request->cancel_url,
                'queue_position' => $request->queue_position ?? null,
                'requestid' => $request->requestid ?? null,
                'image_url' => $request->image_url ?? null,
                'video_url' => $request->video_url ?? null,
                'audio_url' => $audio_url ?? null,
                'parent' => $request->parent ?? null,
                'inference' => $request->inference ?? null,
                'user_id' => Auth::id(),
            ]
        );

        return redirect()->route('frontend.generates.index');
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


    public function getResults($generate){

        $fal = Fal::where('id', $generate->fal_model_id)->first();

        try{
            // Make a GET request to retrieve job results
        $final_response = $this->client->get($generate->response_url, [
            'headers' => [
                'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);  

        $final_result = json_decode($final_response->getBody(), true);
       
   


        if($fal->model_type == 'image'){
            $generate->image_url = $final_result['images'][0]['url'];
            $res['image_url'] = $final_result['images'][0]['url'];
            $res['type'] =  $fal->model_type;
            $res['status'] = "COMPLETED";
            $generate->status = "COMPLETED";
            $generate->save();
            return json_encode($res, true);

        }elseif($fal->model_type == 'video' || $fal->model_type == 'audio'){
            $generate->video_url = $final_result['video']['url'];
            $res['video_url'] = $final_result['video']['url'];
            $res['type'] =  $fal->model_type;
            $res['status'] = "COMPLETED";
            $generate->status = "COMPLETED";
            $generate->save();
            return json_encode($res, true);
        }

       




        return $res;
        
        } catch (Exception $e) {
            //if error 401
            if($e->getCode() == 401){
                $generate->status = "ERROR";
                $generate->save();
               
                \Log::error("Failed to get job status: " . $e->getMessage());
            }
            \Log::error("Failed to get job status: " . $e->getMessage());
        }

        }


    public function status(Request $request){
        
        $generate = Generate::find($request->id);
        
        $client = new Client();
        try {
            // Make a GET request to check job status
            $response = $client->post($generate->status_url, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
            ]);            
            // Return decoded response
            $responseBody = json_decode($response->getBody(), true);
      
             if($responseBody['status'] == "NEW" || $responseBody['status'] == "IN_QUEUE" || $responseBody['status'] == "PROCESSING"){
                
                $result = $this->getResults($generate);
                return $result;

             } elseif($response->getStatusCode() == 200) {
                $result = $this->getResults($generate);
                $generate->status = "COMPLETED";
                $generate->save();
                return $result;

             }
             
           } catch (Exception $e) {
            //if error 401
            if($e->getCode() == 401){
                $generate->status = "ERROR";
                $generate->save();
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            \Log::error("Failed to get job status: " . $e->getMessage());
            return response()->json(['error' => 'Failed to get job status'], 500);
        }

}
}