@extends('layouts.frontend')
@section('content')
<div class="container">
 <div class="row justify-content-center">
<div class="col-md-6">

                        <div class="row">

                        @foreach($childs as $key => $child)
                                <div class="child col-md-12 @if($child->status=='NEW' || $child->status=='IN_QUEUE' || $child->status=='IN_PROGRESS') waiting @elseif($child->status=='COMPLETED' || $child->status=='ERROR') @endif generate_{{$child->id}}" data-id="{{ $child->id }}">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body">
                                         <div class="row">
                                            <div class="col-md-12">
                                               @if($child->fal  && $child->fal->model_type =='video' || $child->fal->model_type =='audio')
                                               <span class="badge badge-success" style="position:absolute; top:0;left:10px;z-index:10;"> <i class="fas fa-video"></i></span>
                                                <video src="{{$child->video_url ?? asset('/images/loading.mp4')}}" controls loop width="100%" height="480" class="video_{{ $child->id }}"></video>
                                               @elseif($child->fal  && $child->fal->model_type =='image')
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
                                                @if($child->fal  && $child->fal->model_type =='image')
                                               @php  $vid = App\Models\Fal::where('model_type', 'video')->first(); @endphp
                                                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.create', ['model_id' => $vid->id, 'image_id'=>$child->id,'parent_id'=>Request::segment(3) ]) }}">
                                                <i class="fas fa-video"></i> {{ _('Generate Video') }} </a>
                                                    <a href="{{ $child->image_url ?? '' }}" class="btn btn-default btn-sm" download><i class="fas fa-download"></i></a>

                                                @elseif($child->fal  && $child->fal->model_type =='video')
                                                
                                                @php  $vid = App\Models\Fal::where('model_type', 'audio')->first(); @endphp

                                                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.create', ['model_id' => $vid->id, 'image_id'=>$child->id,'parent_id'=>Request::segment(3)]) }}">
                                                   <i class="fas fa-music"></i> {{ _('Add Audio') }}
                                                    </a>
                                                    <a href="{{ $child->video_url ?? '' }}" class="btn btn-default btn-sm" download><i class="fas fa-download"></i></a>

                                                @endif

                                                <p> 

                                               <span class="small text-muted"> <strong>{{ trans('cruds.train.fields.created_at') }}:</strong> {{ $child->created_at->diffForHumans() ?? '' }}<br>
                                               <span  class="badge badge-info"> <span id="status_{{$child->id}}">{{ $child->status ?? '' }}</span></span><br>
                                                <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $child->credit ?? '' }}</span>
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



                            @foreach($generates as $key => $generate)
                                <div class="col-md-12 @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') waiting @elseif($generate->status=='COMPLETED' || $generate->status=='ERROR') @endif generate_{{$generate->id}}" data-id="{{ $generate->id }}">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body">
                                         <div class="row">
                                            <div class="col-md-12">
                                               @if($generate->fal  && $generate->fal->model_type =='video' || $generate->fal->model_type =='audio')
                                               <span class="badge badge-success" style="position:absolute; top:0;left:10px;z-index:10;"> <i class="fas fa-video"></i></span>
                                                <video src="{{$generate->video_url ?? asset('/images/loading.mp4')}}" controls loop autoplay width="100%" height="480" class="video_{{ $generate->id }}"></video>
                                               @elseif($generate->fal  && $generate->fal->model_type =='image')
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
                                                @if($generate->fal  && $generate->fal->model_type =='image')
                                               @php  $vid = App\Models\Fal::where('model_type', 'video')->first(); @endphp
                                                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.create', ['model_id' => $vid->id, 'image_id'=>$generate->id,'parent_id'=>Request::segment(3) ]) }}">
                                                <i class="fas fa-video"></i> {{ _('Generate Video') }} </a>
                                                    <a href="{{ $generate->image_url ?? '' }}" class="btn btn-default btn-sm" download><i class="fas fa-download"></i></a>

                                                @elseif($generate->fal  && $generate->fal->model_type =='video' || $generate->fal->model_type =='audio')
                                                
                                                @php  $vid = App\Models\Fal::where('model_type', 'audio')->first(); @endphp

                                                <a class="btn btn-default btn-sm" href="{{ route('frontend.generates.create', ['model_id' => $vid->id, 'image_id'=>$generate->id,'parent_id'=>Request::segment(3) ]) }}">
                                                   <i class="fas fa-music"></i> {{ _('Add Audio') }}
                                                    </a>
                                                    <a href="{{ $generate->video_url ?? '' }}" class="btn btn-default btn-sm" download><i class="fas fa-download"></i></a>

                                                @endif

                                                <p> 

                                               <span class="small text-muted"> <strong>{{ trans('cruds.train.fields.created_at') }}:</strong> {{ $generate->created_at->diffForHumans() ?? '' }}<br>
                                               <span  class="badge badge-info"> <span id="status_{{$generate->id}}">{{ $generate->status ?? '' }}</span></span><br>
                                                <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $generate->credit ?? '' }}</span>
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
                            @endforeach



                        </div>
                    </div>
                </div>
          
            </div>
         
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
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
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
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
  if ($('.waiting').length > 0) {
    setInterval(function() {
        $('.waiting').each(function() {
            var generateId = $(this).data('id');
            $.ajax({
                url: '{{ route('frontend.generates.status') }}',
                method: 'POST',
                data: { id: generateId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    //decode the json response
                    var response = JSON.parse(response);                    
                    if (response.status === 'COMPLETED') {

                        $('.waiting.generate_' + generateId).removeClass('waiting');

                        if (response.type=='video' || response.type=='audio') {

                            $('.video_' + generateId).attr('src', response.video_url);
                            $('.waiting.generate_' + generateId).show();

                        } else if (response.type=='image') {
                            console.log(response);
                            $('.image_' + generateId).attr('src', response.image_url);
                            $('.waiting.generate_' + generateId).show()

                        }
                        $('#status_' + generateId).text('COMPLETED');

                    } else if (response.status === 'ERROR') {
                        
                        $('.waiting.generate_' + generateId).removeClass('waiting').addClass('error');
                        $('#status_' + generateId).text('ERROR');

                    } else if(response.status === 'IN_PROGRESS' || response.status === 'IN_QUEUE' || response.status === 'NEW') {
                       
                        $('#status_' + generateId).text('IN_PROGRESS');

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