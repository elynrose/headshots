@extends('layouts.frontend')
@section('content')
<div class="container">
<h3 class="mb-5">Training History</h3>

    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('train_create')
                <div class="mb-3 d-flex justify-content-left">
                    <a class="btn btn-success" href="{{ route('frontend.trains.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.train.title_singular') }}
                    </a>
                </div>
            @endcan
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="input-group mb-5">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                    </div>
                    <div class="row" id="trainList">
                        @foreach($trains as $train)
                            <div class="col-md-12 mb-4">
                             
                                        <div class="row" data-entry-id="{{ $train->id }}">
                                            <div class="col-md-3">{{ $train->title ?? '' }}</div>
                                            <div class="col-md-2 waiting_{{$train->id}} @if($train->status=='IN_QUEUE' || $train->status =='IN_PROGRESS' || $train->status =='NEW') waiting @elseif($train->status=='COMPLETED'){{'completed'}} @elseif($train->status =='ERROR') error @endif" data-id="{{$train->id}}" data-status="{{$train->status}}" data-url="{{$train->status_url}}"><span class="badge badge-default">@if($train->status == 'NEW') {{'NEW'}}@elseif ($train->status == 'IN_QUEUE') {{'IN QUEUE'}}@elseif($train->status == 'IN_PROGRESS') {{'IN PROGRESS'}} @elseif($train->status == 'COMPLETED') {{'COMPLETED'}} @elseif($train->status == 'ERROR') {{'ERROR'}}  @endif</span></div>
                                            <div class="col-md-2 diffusers_{{$train->id}}">
                                            @if($train->status=='COMPLETED')
                                                <a href="{{ $train->diffusers_lora_file }}" target="_blank" class="small"><i class="fas fa-cog"></i> Diffusers Lora File</a>
                                            @else
                                               <span class="small"> PENDING </span>
                                            @endif
                                            </div>
                                            <div class="col-md-2 zipfile_{{$train->id}}">
                                            @if($train->status=='COMPLETED')
                                                <a href="{{ $train->zipped_file_url }}" target="_blank" class="small"><i class="fas fa-download"></i> Download Photos</a>
                                            @else
                                            <span class="small"> PENDING </span>
                                            @endif
                                            </div>
 
                                            <div class="col-md-2 small">{{ $train->created_at ?? '' }}</div>
                                            <div class="col-md-1">
                                            @can('train_delete')
                                            <form action="{{ route('frontend.trains.destroy', $train->id) }}" method="POST" onsubmit="return confirm ('Are you sure?');" style="display: inline-block;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>    
                                            @endcan
                                            </div>
                                            
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
    .card { border-radius: 10px; }
    .btn-sm { font-size: 0.875rem; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    function sendAjaxRequests() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.waiting').each(function() {
            let id = $(this).data('id');
            let url = $(this).data('url');

            $.ajax({
                url: url,
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'COMPLETED') {
                        let waitingElement = $('.waiting[data-id="' + id + '"]');
                        waitingElement.html('<span class="small badge badge-default">' + response.status + '</span>');
                        waitingElement.removeClass('IN_QUEUE waiting').addClass('COMPLETED');
                        $('.diffusers_' + id).html('<a href="' + response.diffusers_lora_file + '" target="_blank" class="small"><i class="fas fa-cog"></i> Diffusers Lora File</a>');
                        $('.zipfile_' + id).html('<a href="' + response.zipped_file_url + '" target="_blank" class="small"><i class="fas fa-download"></i> Download Photos</a>');
                    } else if (response.status == 'IN_PROGRESS') {
                        $('.waiting[data-id="' + id + '"]').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only">' + response.status + '</span></div>');
                    }
                },
                error: function(xhr, status, error) {
                    let waitingElement = $('.waiting[data-id="' + id + '"]');
                    waitingElement.html('<span class="small badge badge-default">FAILED</span>');
                    waitingElement.removeClass('IN_QUEUE waiting').addClass('FAILED');
                    alert('An unexpected error occurred: Ckeck your API key and credits.');
                }
            });
        });
    }

    // Run the function immediately
    sendAjaxRequests();

    // Set interval to run the function every 15 seconds
    setInterval(sendAjaxRequests, 15000);
});


    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll(".col-md-4");
        
        items.forEach(function(item) {
            let text = item.innerText.toLowerCase();
            item.style.display = text.includes(filter) ? "block" : "none";
        });
    });
</script>
@endsection
