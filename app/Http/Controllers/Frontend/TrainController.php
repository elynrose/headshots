<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTrainRequest;
use App\Http\Requests\StoreTrainRequest;
use App\Http\Requests\UpdateTrainRequest;
use App\Models\Train;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;


class TrainController extends Controller
{
    private $client;
    private $apiKey;

        /**
     * Create a new job instance.
     *
     * @param Train $model The training model instance.
     * @return void
     */
    public function __construct(Train $model)
    {
        $this->apiKey = env('FAL_AI_API_KEY');
                
        // Initialize HTTP client
        $this->client = new Client();
    }

    public function index()
    {
        abort_if(Gate::denies('train_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trains = Train::with(['user'])
        ->where('user_id', Auth::id())
        ->get();

        return view('frontend.trains.index', compact('trains'));
    }

    public function create()
    {
        abort_if(Gate::denies('train_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.trains.create', compact('users'));
    }

    public function store(StoreTrainRequest $request)
    {
        // Check if the user already has a training in progress
        $existingTrain = Train::where('user_id', $request->user_id)
            ->whereIn('status', ['NEW', 'IN_QUEUE', 'IN_PROGRESS', 'COMPLETED'])
            ->first();

        if ($existingTrain) {
            return redirect()->route('frontend.trains.index')
            ->withErrors(['error' => 'You are only allowed to create one training.']);
        }

        $train = Train::create(
            [
            'user_id' => $request->user_id,
            'title' => $request->title,
            'status'=> 'NEW',
            ]
        );

        return redirect()->route('frontend.trains.index');
    }

    public function edit(Train $train)
    {
        abort_if(Gate::denies('train_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $train->load('user');

        return view('frontend.trains.edit', compact('train', 'users'));
    }

    public function update(UpdateTrainRequest $request, Train $train)
    {
        $train->update($request->all());

        return redirect()->route('frontend.trains.index');
    }

    public function show(Train $train)
    {
        abort_if(Gate::denies('train_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $train->load('user');

        return view('frontend.trains.show', compact('train'));
    }

    public function destroy(Train $train)
    {
        abort_if(Gate::denies('train_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $train->delete();

        return back();
    }

    public function massDestroy(MassDestroyTrainRequest $request)
    {
        $trains = Train::find(request('ids'));

        foreach ($trains as $train) {

            $train->delete();
            
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function status(Request $request)
    {
        $train = Train::find($request->id);

        if( $train->status == 'NEW') {
            $this->submitTrainingJob($train);
        }elseif( $train->status == 'IN_QUEUE') {
            $this->getJobStatus($train);
        } elseif( $train->status == 'IN_PROGRESS') {
            $this->getResults($train);
        } elseif( $train->status == 'COMPLETED') {
           return json_encode($train);
        }

      
    }

    public function getJobStatus($train){
        try {
            // Make a GET request to check job status
            $response = $this->client->get($train->status_url, [
                'headers' => [
                    'Authorization' => 'Key ' . $this->apiKey,
                ],
            ]);
            // Return decoded response
           $result =  json_decode($response->getBody()->getContents(), true);
           
           $train->status = $result['status'];
           $train->save();
        
           return $result;

        } catch (Exception $e) {
            \Log::error("Failed to get job status: " . $e->getMessage());
            $train->status = "ERROR";
            $train->save();
            return null;
        }
    }

    public function getResults($train){

        try{
            // Make a GET request to retrieve job results
        $final_response = $this->client->get($train->response_url, [
            'headers' => [
                'Authorization' => 'Key ' . $this->apiKey,
            ],
        ]);


        $final_result = json_decode($final_response->getBody()->getContents(), true);

        $train->config_file = $final_result['config_file']['url'];
        $train->diffusers_lora_file = $final_result['diffusers_lora_file']['url'];
        $train->status = "COMPLETED";
        $train->save();
        
        } catch (Exception $e) {
            //if error 401
            if($e->getCode() == 401){
                $train->status = "ERROR";
                $train->save();
                \Log::error("Failed to get job status: " . $e->getMessage());
            }
            \Log::error("Failed to get job status: " . $e->getMessage());
        }

        }


    /**
     * Submit the training job to an external API.
     *
     * @param string $url
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function submitTrainingJob($train)
    {
        try {
            // Make a POST request to submit the training job
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://queue.fal.run/fal-ai/flux-lora-fast-training', [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'images_data_url' => $train->zipped_file_url,
                ],
            ]);
            
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            if ($responseData !== null) {
                // Update model with response data from training API
                $train->status = $responseData['status'];
                $train->requestid = $responseData['request_id'];
                $train->status_url = $responseData['status_url'];
                $train->response_url = $responseData['response_url'];
                $train->cancel_url = $responseData['cancel_url'];
                $train->queue_position = $responseData['queue_position'];
                $train->save();
            } else {
                \Log::error('Failed to decode JSON response: ' . $responseData);
            }

        } catch (Exception $e) {
            //$train->status = "ERROR";
            $train->update(['status' => 'ERROR']);
            \Log::error('Training job submission failed: ' . $e->getMessage());
        }
    }
}
