@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('photo_create')
                <div class="action-bar mb-5">
                    <a class="btn btn-success" href="{{ route('frontend.photos.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.photo.title_singular') }}
                    </a>
                </div>
            @endcan
            
            <div class="card shadow-sm">
      
                
                <div class="card-body">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search...">
                    </div>
                    <div class="photo-list" id="photoList">
                        @foreach($photos as $photo)
                            <div class="photo-item" data-entry-id="{{ $photo->id }}">
                                <div class="photo-thumbnail">
                                    @foreach($photo->photo as $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank">
                                            <img src="{{ $media->getUrl('preview') }}" alt="Photo">
                                        </a>
                                    @endforeach
                                </div>
                                <div class="photo-details">
                                    <label>
                                        <input type="checkbox" disabled {{ $photo->use_for_training ? 'checked' : '' }}> {{ trans('cruds.photo.fields.use_for_training') }}
                                    </label>
                                </div>
                                <div class="photo-actions">
                                    @can('photo_show')
                                        <a class="btn btn-default btn-sm" href="{{ route('frontend.photos.show', $photo->id) }}"><i class="fas fa-eye"></i></a>
                                    @endcan
                                    @can('photo_edit')
                                        <a class="btn btn-default btn-sm" href="{{ route('frontend.photos.edit', $photo->id) }}"><i class="fas fa-edit"></i></a>
                                    @endcan
                                    @can('photo_delete')
                                        <form action="{{ route('frontend.photos.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-default btn-sm" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .photo-list { display: flex; flex-wrap: wrap; gap: 20px; }
    .photo-item { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); padding: 15px; display: flex; flex-direction: column; align-items: center; width: 250px; }
    .photo-thumbnail img { width: 100%; border-radius: 5px; }
    .photo-details { margin: 10px 0; }
    .photo-actions { display: flex; gap: 10px; }
    .search-bar { margin-bottom: 15px; }
    .search-bar input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
</style>
@endsection

@section('scripts')
<script>
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll(".photo-item");
        
        items.forEach(function(item) {
            let text = item.innerText.toLowerCase();
            item.style.display = text.includes(filter) ? "flex" : "none";
        });
    });
</script>
@endsection
