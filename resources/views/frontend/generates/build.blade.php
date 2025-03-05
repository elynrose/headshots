@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') waiting @elseif($generate->status=='COMPLETED' || $generate->status=='ERROR') @endif generate_{{$generate->id}}" data-id="{{ $generate->id }}">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    @if($generate->fal && $generate->fal->model_type == 'image' || $generate->fal->model_type == 'upscale')
                                        <span class="badge badge-warning" style="position:absolute; top:0;left:0;z-index:10;"><i class="fas fa-photo"></i></span>
                                        <a href="{{ $generate->image_url ?? '' }}" style="width:100%; height:375px; display:block; overflow:hidden;">
                                            <img src="{{ $generate->image_url ?? asset('images/loading.gif') }}" class="img-fluid image_{{$generate->id}} d-block mx-auto" alt="{{$generate->title}}" loading="lazy">
                                        </a>
                                    @else
                                        {{ _('Model Undefined') }}
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <p class="py-2">
                                        @if($generate->fal && $generate->fal->model_type == 'image')
                                            @php $vid = App\Models\Fal::where('model_type', 'video')->first(); @endphp
                                            <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.createWithParent', ['model_id' => $vid->id, 'image_id' => $generate->id, 'parent_id' => Request::segment(3)]) }}">
                                                <i class="fas fa-video"></i> {{ _('Convert to Video') }}
                                            </a>
                                            <a href="{{ $generate->image_url ?? '' }}" class="btn btn-default btn-sm" download><i class="fas fa-download"></i></a>
                                        @elseif($generate->fal && ($generate->fal->model_type == 'video' || $generate->fal->model_type == 'audio'))
                                            @php $vid = App\Models\Fal::where('model_type', 'audio')->first(); @endphp
                                            <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.createWithParent', ['model_id' => $vid->id, 'image_id' => $generate->id, 'parent_id' => Request::segment(3)]) }}">
                                                <i class="fas fa-music"></i> {{ _('Add Lip Sync') }}
                                            </a>
                                            <a href="{{ $generate->video_url ?? '' }}" class="btn btn-default btn-sm" download><i class="fas fa-download"></i></a>
                                        @endif
                                    </p>
                                    <p>
                                        <span class="small text-muted">
                                            <strong>{{ trans('cruds.train.fields.created_at') }}:</strong> {{ $generate->created_at->diffForHumans() ?? '' }}<br>
                                            <span class="badge badge-info"><span id="status_{{$generate->id}}">{{ $generate->status ?? '' }}</span></span><br>
                                            <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $generate->credit ?? '' }}
                                        </span>
                                    </p>
                                    @can('generate_delete')
                                        <form action="{{ route('frontend.generates.destroy', $generate->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-danger btn-xs" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($childs as $key => $child)
                    <div class="child col-md-12 @if($child->status=='NEW' || $child->status=='IN_QUEUE' || $child->status=='IN_PROGRESS') waiting @elseif($child->status=='COMPLETED' || $child->status=='ERROR') @endif generate_{{$child->id}}" data-id="{{ $child->id }}">
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="row" style="min-height:350px;">
                                    <div class="col-md-12">
                                        @if($child->fal && ($child->fal->model_type == 'video' || $child->fal->model_type == 'audio') || $child->fal->model_type == 'upscale')
                                            <div class="loading-gif loader_{{$generate->id}} text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><img width="50" src="{{asset('images/loader.gif')}}"></div>
                                            <span class="badge badge-success" style="position:absolute; top:0;left:10px;z-index:10;"><i class="fas fa-video"></i></span>
                                            <div class="loading-gif loader_{{$generate->id}} text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><img width="50" src="{{asset('images/loader.gif')}}"></div>
                                            <video src="{{$child->video_url ?? asset('/images/loading.mp4')}}" controls loop width="100%" height="480" class="videos video_{{ $child->id }}" @if($child->status!=='COMPLETED') style="display:none;" @endif></video>
                                        @elseif($child->fal && $child->fal->model_type == 'image')
                                            <span class="badge badge-warning" style="position:absolute; top:0;left:0;z-index:10;"><i class="fas fa-photo"></i></span>
                                            <a href="{{ $child->image_url ?? '' }}" style="width:100%; height:225px; display:block; overflow:hidden;">
                                                <img src="{{ $child->image_url ?? asset('images/loading.gif') }}" class="img-fluid image_{{$child->id}} d-block mx-auto" alt="{{$child->title}}" loading="lazy">
                                            </a>
                                        @else
                                            {{ _('Model Undefined') }}
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <p class="py-2">
                                        @php
    $models = App\Models\Fal::whereIn('model_type', ['video', 'audio', 'upscale'])->get()->keyBy('model_type');
    $parentId = Request::segment(3);
@endphp

@if($child->fal)
    @switch($child->fal->model_type)
        @case('image')
            @if(isset($models['video']))
                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.createWithParent', [
                    'model_id' => $models['video']->id,
                    'image_id' => $child->id,
                    'parent_id' => $generate_id
                ]) }}">
                    <i class="fas fa-video"></i> {{ __('Convert to Video') }}
                </a>
            @endif
            <a href="{{ $child->image_url ?? '' }}" class="btn btn-default btn-sm" download>
                <i class="fas fa-download"></i>
            </a>
            @break

        @case('video')
            @if(isset($models['audio']))
                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.createWithParent', [
                    'model_id' => $models['audio']->id,
                    'image_id' => $child->id,
                    'parent_id' => $parentId
                ]) }}">
                    <i class="fas fa-music"></i> {{ __('Add Lip Sync') }}
                </a>
            @endif

            @if(isset($models['upscale']))
                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.createWithParent', [
                    'model_id' => $models['upscale']->id,
                    'image_id' => $child->id,
                    'parent_id' => $parentId
                ]) }}">
                    <i class="fas fa-expand"></i> {{ __('Upscale') }}
                </a>
            @endif

            <a href="{{ $child->video_url ?? '' }}" class="btn btn-default btn-sm" download>
                <i class="fas fa-download"></i>
            </a>
            @break
    @endswitch
@endif


                                        </p>
                                        <p>
                                            <span class="small text-muted">
                                                <strong>{{ trans('cruds.train.fields.created_at') }}:</strong> {{ $child->created_at->diffForHumans() ?? '' }}<br>
                                                <span class="badge badge-info"><span id="status_{{$child->id}}">{{ $child->status ?? '' }}</span></span><br>
                                                <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $child->credit ?? '' }}
                                            </span>
                                        </p>
                                        @can('generate_delete')
                                            <form action="{{ route('frontend.generates.destroy', $child->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-danger btn-xs" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endcan
                                    </div>
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

@section('scripts')
@parent
<script>
$(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    @can('generate_delete')
    let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
    let deleteButton = {
        text: deleteButtonTrans,
        url: "{{ route('frontend.generates.massDestroy') }}",
        className: 'btn-danger',
        action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
            });

            if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
                return
            }

            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () { location.reload() })
            }
        }
    }
    dtButtons.push(deleteButton)
    @endcan

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 100,
    });
    let table = $('.datatable-Generate:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

    $(function(){
        $('.loading-gif').addClass('hide');
    });

    if ($('.waiting').length > 0) {
        var checkInterval = setInterval(function() {
            if ($('.waiting').length === 0) {
                clearInterval(checkInterval); // Stop polling if no items are waiting
                return;
            }
            $('.waiting').each(function() {
                var generateId = $(this).data('id');
                var loaderId = $('.waiting').last().data('id');
                $('.loader_' + loaderId).removeClass('hide');
                $.ajax({
                    url: '{{ route('frontend.generates.status') }}',
                    method: 'POST',
                    data: { id: generateId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);

                        if (response.status == 'COMPLETED') {
                            $('.waiting.generate_' + generateId).removeClass('waiting');
                            $('.loader_' + loaderId).addClass('hide');

                            if (response.type === 'video' || response.type === 'audio') {
                                var videoElement = $('.video_' + generateId);
                                if (response.video_url) {
                                    var timestamp = new Date().getTime(); // Prevent caching issues
                                    videoElement.attr('src', response.video_url + "?t=" + timestamp);
                                    videoElement[0].load();  // Reload video
                                    videoElement.show();  // Ensure it's visible
                                } else {
                                    console.log("Error: Video URL is missing for ID:", generateId);
                                }
                            } else if (response.type === 'image') {
                                $('.image_' + generateId).attr('src', response.image_url);
                                $('.waiting.generate_' + generateId).show();
                                $('.loader_' + generateId).hide();
                            }
                            $('#status_' + generateId).text('COMPLETED');
                        } else if (response.status === 'ERROR') {
                            $('.waiting.generate_' + generateId).removeClass('waiting').addClass('error');
                            $('#status_' + generateId).text('ERROR');
                        } else {
                            $('#status_' + generateId).text(response.status);
                        }
                    },
                    error: function() {
                        console.log('Error fetching status for generate ID:', generateId);
                    }
                });
            });
        }, 5000);
    }
});
</script>
@endsection
