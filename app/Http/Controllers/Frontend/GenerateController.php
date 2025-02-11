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

        $generates = Generate::with(['train', 'user'])->get();

        return view('frontend.generates.index', compact('generates'));
    }

    public function create()
    {
        abort_if(Gate::denies('generate_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trains = Train::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.generates.create', compact('trains', 'users'));
    }

    public function store(StoreGenerateRequest $request)
    {
        $generate = Generate::create($request->all());

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

        try{
            // Make a GET request to retrieve job results
        $final_response = $this->client->get($generate->response_url, [
            'headers' => [
                'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);  
        $final_result = json_decode($final_response->getBody(), true);

        // Update the generate model with the final results
        $generate->status = "COMPLETED";
        $generate->image_url = $final_result['images'][0]['url'];
        $generate->save();

        //add status= completed to the final result array
        $final_result['status'] = "COMPLETED";

        return $final_result;



       // return $final_result;
        
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

             } else {
                
                $generate->status = $responseBody['status'];
                $generate->save();
             }
           
        } catch (Exception $e) {
        
        }
    }

}
