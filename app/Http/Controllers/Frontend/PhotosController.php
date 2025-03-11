<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPhotoRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class PhotosController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('photo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $photos = Photo::with(['user', 'media'])->get();

        return view('frontend.photos.index', compact('photos'));
    }

    public function create()
    {
        abort_if(Gate::denies('photo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.photos.create', compact('users'));
    }

    public function store(StorePhotoRequest $request)
    {
        //Check if user has more than 10 photos
        $user = auth()->user();
        $photos = Photo::where('user_id', $user->id)->get();
        if ($photos->count() >= 10) {
            return redirect()->route('frontend.photos.index')->with('error', 'You have reached the maximum number of photos allowed.');
        }
        $photo = Photo::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $photo->addMedia(storage_path('tmp/uploads/' . basename($file)))
                  ->toMediaCollection('photo', 'cloud')
                  ->setCustomProperty('visibility', 'public');
            
            $key = $photo->getMedia('photo')->first()->getKey();
            $filename = $photo->getMedia('photo')->first()->file_name;

            $name = $key . '/' . $filename;
            $amz_url = env('AWS_FILE_URL') . $name;


            //Get the url of the uploaded file
           // $url = $photo->getMedia('photo')->first()->getUrl($key);

            //Update the model with the url
            $photo->update(['url' => $amz_url, 'temporary_amz_url' => $amz_url]);


             /*
    $client = new Client();
    try {
        if (file_exists($filePath)) {
            // Make a GET request to check job status
            $response = $client->post('https://api.fal.ai/storage/upload', [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => file_get_contents($filePath),
                        'filename' => 'hello.txt',
                    ],
                ],
            ]);            
            // Return decoded response
            $responseBody = json_decode($response->getBody(), true);
            return $responseBody;
        } else {
            return ['error' => 'File not found'];
        }

    } catch (RequestException $e) {
        // Catch any request exceptions
        if ($e->hasResponse()) {
            // Get the response
            $response = $e->getResponse();
            return ['error' => 'File upload failed', 'status' => $response->getStatusCode()];
        }
    }*/
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $photo->id]);
        }

        return redirect()->route('frontend.photos.index');
    }

    public function edit(Photo $photo)
    {
        abort_if(Gate::denies('photo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $photo->load('user');

        return view('frontend.photos.edit', compact('photo', 'users'));
    }

    public function update(UpdatePhotoRequest $request, Photo $photo)
    {
        $photo->update($request->all());

        if (count($photo->photo) > 0) {
            foreach ($photo->photo as $media) {
                if (! in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $photo->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $photo->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('frontend.photos.index');
    }

    public function show(Photo $photo)
    {
        abort_if(Gate::denies('photo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $photo->load('user');

        return view('frontend.photos.show', compact('photo'));
    }

    public function destroy(Photo $photo)
    {
        abort_if(Gate::denies('photo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $photo->delete();
        //Delete from S3
        $photo->clearMediaCollection('photo');

        return back();
    }

    public function massDestroy(MassDestroyPhotoRequest $request)
    {
        $photos = Photo::find(request('ids'));

        foreach ($photos as $photo) {
            $photo->delete();
            //Delete from S3
            $photo->clearMediaCollection('photo');

        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('photo_create') && Gate::denies('photo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Photo();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
