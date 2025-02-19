@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row">
    <h3 class="mb-5">Training Gallery</h3>

        <div class="col-md-12">
            @can('photo_create')
                <div class="action-bar mb-5">
                    <a class="btn btn-success" href="{{ route('frontend.photos.create') }}">
                       <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('cruds.photo.title_singular') }}
                    </a>
                </div>
            @endcan
 
                    <div class="row" id="photoList">
                        @foreach($photos as $photo)
                            <div class="col-md-3 mb-4 photo-item" data-entry-id="{{ $photo->id }}">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="photo-thumbnail">
                                            @foreach($photo->photo as $media)
                                                <a href="{{ $media->getUrl() }}" target="_blank">
                                                    <img src="{{ $media->getUrl('preview') }}" alt="Photo" class="img-fluid" width="100%">
                                                </a>
                                            @endforeach
                                        </div>
                                        <div class="photo-details mt-2">
                                            <label>
                                                <input disabled class="photos_{{$photo->id}} photo" data-id="{{$photo->id}}" type="checkbox" {{ $photo->use_for_training ? 'checked' : '' }}> {{ trans('cruds.photo.fields.use_for_training') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="photo-actions">
                                            @can('photo_show')
                                                <a class="btn btn-primary btn-sm" href="{{ route('frontend.photos.show', $photo->id) }}"><i class="fas fa-eye"></i></a>
                                            @endcan
                                            @can('photo_edit')
                                                <a class="btn btn-info btn-sm" href="{{ route('frontend.photos.edit', $photo->id) }}"><i class="fas fa-edit"></i></a>
                                            @endcan
                                            @can('photo_delete')
                                                <form action="{{ route('frontend.photos.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" class="d-inline">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .photo-thumbnail img { border-radius: 5px; }
    .photo-details { margin-top: 10px; }
    .photo-actions { display: flex; gap: 5px; justify-content: center; }
</style>
@endsection

@section('scripts')
<script>
 
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll(".photo-item");
        
        items.forEach(function(item) {
            let text = item.innerText.toLowerCase();
            item.style.display = text.includes(filter) ? "block" : "none";
        });
    });
</script>
@endsection
