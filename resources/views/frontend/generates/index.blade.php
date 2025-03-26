@extends('layouts.frontend')
@section('content')
<div class="container">

    <div class="row justify-content-center">
    
           
                <div>
                    <div>
                        <div class="row">
                            @foreach($generates as $key => $generate)
                                <div class="col-md-4 @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') waiting @elseif($generate->status=='COMPLETED' || $generate->status=='ERROR') @endif generate_{{$generate->id}}" data-id="{{ $generate->id }}">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body">
                                         <div class="row">
                                            <div class="col-md-12">
                                                <div class="loading-gif loader_{{$generate->id}} text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><img width="50" src="{{asset('images/loader.gif')}}"></div>
                                               @if($generate  && $generate->content_type =='video' || $generate->content_type =='audio')
                                               <span class="badge badge-success" style="position:absolute; top:0;left:10px;z-index:10;"> <i class="fas fa-video"></i></span>
                                                <video src="{{$generate->video_url ?? ''}}" controls loop  width="100%" height="225" class="video_{{ $generate->id }}"></video>
                                               @elseif($generate  && $generate->content_type =='image' || $generate->content_type =='prompt' || $generate->content_type =='train')
                                               <span class="badge badge-warning" style="position:absolute; top:0;left:0;z-index:10;"><i class="fas fa-photo"></i> {{ $generate->fal->model_name}}</span>
                                              <a href="{{route('frontend.generates.build', ['model_id'=>$generate->fal_model_id, 'generate_id'=>$generate->id])}}" style="width:100%; height:225px; display:block; overflow:hidden;"> 
                                                <img src="{{ $generate->image_url ?? '' }}" class="img-fluid image_{{$generate->id}} d-block mx-auto" alt="{{$generate->title}}" loading="lazy">
                                             </a> 
                                                  @else
                                                    {{ _('Model Undefined') }}
                                                  @endif
                                            </div>
                                            <div class="col-md-12">
                                            <p class="py-2">
                                                @if($generate && $generate->content_type =='image' || $generate->content_type =='prompt' || $generate->content_type =='train')
                                               @php  $vid = App\Models\Fal::where('model_type', 'video')->first(); @endphp
                                               
                                                    <a href="{{ $generate->image_url ?? '' }}" class="btn btn-default btn-xs" download><i class="fas fa-download"></i></a>
                                                    <a href="{{route('frontend.generates.build', ['model_id'=>$generate->fal_model_id, 'generate_id'=>$generate->id])}}" class="btn btn-default btn-xs"><i class="fas fa-random"></i> Convert</a>


                                                @elseif($generate && $generate->content_type =='video' || $generate->content_type =='audio')
                                                
                                                @php  $vid = App\Models\Fal::where('model_type', 'audio')->first(); @endphp
                                               
                                                    <a href="{{ $generate->video_url ?? '' }}" class="btn btn-default btn-xs" download><i class="fas fa-download"></i></a>

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
                <div class="d-flex justify-content-center">
            {{ $generates->links('pagination::bootstrap-4') }}
        </div>
            </div>
         
        </div>
     
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
$(document).ready(function () {
    $('.loading-gif').hide();

    if ($('.waiting').length > 0) {
        setInterval(checkGenerationStatus, 5000);
    }
});

function checkGenerationStatus() {
    $('.waiting').each(function () {
        var $element = $(this);
        var generateId = $element.data('id');
        var $loader = $('.loader_' + generateId);
        var $status = $('#status_' + generateId);
        
        $loader.show();

        $.ajax({
            url: '{{ route('frontend.generates.status') }}',
            method: 'POST',
            data: { id: generateId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                updateStatus($element, generateId, response);
            },
            error: function (xhr, status, error) {
                handleError($element, generateId, xhr);
            }
        });
    });
}

function updateStatus($element, generateId, response) {
    var $loader = $('.loader_' + generateId);
    var $status = $('#status_' + generateId);

    if (response.status === 'COMPLETED') {
        $element.removeClass('waiting');
        $loader.hide();
        $status.text('COMPLETED');

        if (['video', 'audio'].includes(response.type)) {
            $('.video_' + generateId).attr('src', response.video_url).show();
        } else if (['image', 'prompt', 'train'].includes(response.type)) {
            $('.image_' + generateId).attr('src', response.image_url).show();
        }

    } else if (response.status === 'ERROR') {
        handleError($element, generateId, response);
        location.reload();

    } else if (['IN_PROGRESS', 'IN_QUEUE', 'NEW'].includes(response.status)) {
        $status.text('IN_PROGRESS');
    }
}

function handleError($element, generateId, response) {
    var $loader = $('.loader_' + generateId);
    var $status = $('#status_' + generateId);

    $element.removeClass('waiting').addClass('error');
    $status.text('ERROR');
    $loader.hide();

    // Extract and display error message if available
    var errorMessage = response?.detail || 'An unknown error occurred. Please check your credit balance or try again.';
    alert(errorMessage);
}


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
  
})

</script>
@endsection