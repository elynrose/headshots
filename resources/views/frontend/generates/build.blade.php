@extends('layouts.frontend')
<style>
    .container {
        position: relative;
    }

    .parent {
        position: relative;
        margin-bottom: 50px; /* Space for the connecting line */
    }

    .child {
        position: relative;
        margin-top: 50px; /* Space for the connecting line */
    }

    .parent::after,
    .child::before {
        content: '';
        position: absolute;
        width: 2px;
        background-color: #efefef; /* Line color */
    }

    .parent::after {
        top: 100%;
        left: 50%;
        height: 50px; /* Length of the line */
    }

    .child::before {
        bottom: 100%;
        left: 50%;
        height: 50px; /* Length of the line */
    }
    
</style>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') waiting @elseif($generate->status=='COMPLETED' || $generate->status=='ERROR') @endif generate_{{$generate->id}}" data-id="{{ $generate->id }}">
                    <div class="card shadow-sm mb-3"  id="parent_{{$generate->id}}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    @if($gen->checkTypes($generate->content_type))
                                        <span class="badge badge-warning" style="position:absolute; top:0;left:0;z-index:10;"><i class="fas fa-photo"></i> {{ $generate->fal->model_name}}</span>
                                        <a href="{{ $generate->image_url ?? '' }}" style="width:100%; max-height:500px; display:block;">
                                            <img src="{{ $generate->image_url ?? asset('images/loading.gif') }}" class="img-fluid image_{{$generate->id}} d-block mx-auto" alt="{{$generate->title}}" loading="lazy">
                                        </a>
                                    @else
                                        {{ _('Model Undefined') }}
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <p class="py-2">
 
                                    @if(in_array($generate->content_type, $gen->imageTypes()))
                                     
                                     @foreach($gen->videoTypes() as $type)
                                     @php  $fal = App\Models\Fal::where('model_type', $type)->first(); @endphp
                                     <a class="btn btn-default btn-xs" href="{{ route('frontend.generates.createWithParent', ['model_id' => $fal->id, 'image_id' => $generate->id, 'parent_id' => Request::segment(3) ]) }}">
                                         <i class="fas {{ $fal->icon ?? 'fa-cog' }}"></i> {{ $fal->title }}
                                     </a>
                                     @endforeach
                                     @elseif(in_array($generate->content_type, $gen->imageTypes()))
                                     @foreach($gen->videoTypes() as $type)
                                     @php  $fal = App\Models\Fal::where('model_type', $type)->first(); @endphp
                                     <a class="btn btn-default btn-xs" href="{{ route('frontend.generates.createWithParent', ['model_id' => $fal->id, 'image_id' => $generate->id, 'parent_id' => Request::segment(3) ]) }}">
                                         <i class="fas {{ $fal->icon ?? 'fa-cog' }}"></i> {{ $fal->title }}
                                     </a>
                                     @endforeach
                                    
                                     @endif

                                        <a href="{{ $generate->image_url ?? '' }}" class="btn btn-default btn-xs" download><i class="fas fa-download"></i></a>
                                       
                                    </p>
                                    <p><span class="badge badge-info"><span id="status_{{$generate->id}}">{{ $generate->status ?? '' }}</span></p>
                                    <p class="small text-muted">{{ $generate->prompt ?? '' }}</p>
                                    <p>
                                        <span class="small text-muted">
                                            <strong>{{ trans('cruds.train.fields.created_at') }}:</strong> {{ $generate->created_at->diffForHumans() ?? '' }}<br>
                                            <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $generate->credit ?? '0' }}
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
</div>
</div>
</div>
</div>
</div>
<div class="row justify-content-center">
    <div class="col-md-9 col-sm-12">
        <div class="row">
                @foreach($childs as $key => $child)
                    <div class="child col-md-6 @if($child->status=='NEW' || $child->status=='IN_QUEUE' || $child->status=='IN_PROGRESS') waiting @elseif($child->status=='COMPLETED' || $child->status=='ERROR') @endif generate_{{$child->id}}" data-id="{{ $child->id }}">
                        <div class="card shadow-sm mb-3 h-100" id="child_{{$child->id}}">
                            <div class="card-body">
                                <div class="row" style="min-height:500px;">
                                    <div class="col-md-12">
                                    @if(in_array($child->content_type, $gen->videoTypes()))
                                    <div class="loading-gif loader_{{$child->id}} text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><img width="50" src="{{asset('images/loader.gif')}}"></div>
                                            <span class="badge badge-success" style="position:absolute; top:0px;left:10px;z-index:10;"><i class="fas fa-video"></i>  {{ $child->fal->model_name}}</span>
                                            <div class="loading-gif loader_{{$child->id}} text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><img width="50" src="{{asset('images/loader.gif')}}"></div>
                                            <video src="{{$child->video_url }}" controls loop width="100%" height="500" class="videos video_{{ $child->id }}" @if($child->status!=='COMPLETED') style="display:none;" @endif></video>
                                        @elseif(in_array($child->content_type, $gen->imageTypes()))
                                            <span class="badge badge-warning" style="position:absolute; top:55px;left:0;z-index:10;"><i class="fas fa-photo"></i>  {{ $child->fal->model_name}}</span>
                                            <a href="{{ $child->image_url ?? '' }}" style="width:100%; height:500px; display:block; overflow:hidden;">
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

                                           
                                                
                                        @if($generate->content_type=='video' || $generate->content_type=='audio' || $generate->content_type=='upscale')
                                     
                                        @foreach($gen->imageTypes() as $type)
                                        @php  $fal = App\Models\Fal::where('model_type', $type)->first(); @endphp
                                        <a class="btn btn-default btn-xs" href="{{ route('frontend.generates.createWithParent', ['model_id' => $fal->id, 'image_id' => $child->id, 'parent_id' => $child->parent ]) }}">
                                            <i class="fas {{ $fal->icon ?? 'fa-cog' }}"></i> {{ $fal->title }}
                                        </a>
                                        @endforeach
                                        @elseif($generate->content_type=='image' || $generate->content_type=='prompt' || $generate->content_type=='train' || $generate->content_type=='background')
                                        @foreach($gen->videoTypes() as $type)
                                        @php  $fal = App\Models\Fal::where('model_type', $type)->first(); @endphp
                                        <a class="btn btn-default btn-xs" href="{{ route('frontend.generates.createWithParent', ['model_id' => $fal->id, 'image_id' => $child->id, 'parent_id' => $child->parent ]) }}">
                                            <i class="fas {{ $fal->icon ?? 'fa-cog' }}"></i> {{ $fal->title }}
                                        </a>
                                        @endforeach
                                       
                                        @endif
                                        
                                        @if(in_array($generate->content_type, $gen->imageTypes()))
                                        <a href="{{ $child->video_url ?? '' }}" class="btn btn-default btn-xs" download><i class="fas fa-download"></i></a>
                                        @endif  

                                        @if(in_array($generate->content_type, $gen->videoTypes()))
                                        <a href="{{ $child->image_url ?? '' }}" class="btn btn-default btn-xs" download><i class="fas fa-download"></i></a>
                                        @endif  

                                        </p>
                                        <span class="badge badge-info"><span id="status_{{$child->id}}">{{ $child->status ?? '' }}</span></span><br>

                                        <p>
                                            
                                                <p class="text-muted small">{{$child->prompt ?? ''}}</p>
                                              <span class="small text-muted">  <strong>{{ trans('cruds.train.fields.created_at') }}:</strong> {{ $child->created_at->diffForHumans() ?? '' }}<br>
                                                <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $child->credit ?? '0' }}<br>
                                                <span><span id="queue_{{$child->id}}">Queue position: {{ $child->queue_position ?? 'Waiting...' }}</span></span><br>

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
                $('.loading-gif, .loader_' + loaderId).removeClass('hide');

                $.ajax({
                    url: '{{ route('frontend.generates.status') }}',
                    method: 'POST',
                    data: { id: generateId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        $('#queue_' + generateId).text('Queue position: ' + response.queue_position);

                        if (response.status == 'COMPLETED') {
                            $('.waiting.generate_' + generateId).removeClass('waiting');
                            $('.loader_' + loaderId).addClass('hide');

                            if (response.type === 'video' || response.type === 'audio' || response.type === 'upscale') {
                                var videoElement = $('.video_' + generateId);
                                if (response.video_url) {
                                    var timestamp = new Date().getTime(); // Prevent caching issues
                                    videoElement.attr('src', response.video_url + "?t=" + timestamp);
                                    videoElement[0].load();  // Reload video
                                    videoElement.show();  // Ensure it's visible
                                    $('.loading-gif, .loader_' + generateId).hide();
                                } else {
                                    console.log("Error: Video URL is missing for ID:", generateId);
                                }
                            } else if (response.type === 'image' || response.type === 'prompt' || response.type === 'train' || response.type === 'background') {
                                $('.image_' + generateId).attr('src', response.image_url);
                                $('.waiting.generate_' + generateId).show();
                                $('.loading-gif, .loader_' + generateId).hide();
                            }
                            $('#status_' + generateId).text('COMPLETED');
                        } else if (response.status === 'ERROR') {
                            $('.waiting.generate_' + generateId).removeClass('waiting').addClass('error');
                            $('#status_' + generateId).text('ERROR');
                            $('.loading-gif, .loader_' + generateId).hide();
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
