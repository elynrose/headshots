<?php

namespace App\Jobs;

use App\Models\Train;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Http\Request;

class GetGenerateRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The model instance associated with the job.
     *
     * @var Train
     */
    protected $model;

    /**
     * API Key for authentication.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Base URL for the API endpoint.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * HTTP client instance.
     *
     * @var Client
     */
    protected $client;
    protected $urlWithWebhook;

    /**
     * Create a new job instance.
     *
     * @param Train $model The training model instance.
     * @return void
     */
    public function __construct(Train $model)
    {
        $this->model = $model;
        $this->apiKey = env('FAL_AI_API_KEY');
        
        // Set the API base URL from environment or use default
        $this->baseUrl = env('FAL_AI_BASE_URL', 'https://queue.fal.run/fal-ai/flux/dev');
        
        // Initialize HTTP client
        $this->client = new Client();

        $this->urlWithWebhook = $this->base_url . '?webhook=' . env('APP_URL') . '/api/webhook';

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $generate = Generate::find($request->id);
        $client = new Client();
        try {
            // Make a GET request to check job status
            $response = $client->post($this->urlWithWebhook, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
            ]);            
            // Return decoded response
            $responseBody = json_decode($response->getBody(), true);
      
             if($responseBody['status'] == "OK"){
                
               $generate->status = "NEW";
               $generate->request_id = $responseBody['request_id'];
                $generate->save();

                return $result;


             }
           
        } catch (Exception $e) {
        
        }
    }


    public function status(Request $request){
        
       
    }

}
