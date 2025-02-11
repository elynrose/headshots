@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('generate_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.generates.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.generate.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
           
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="row">
                            @foreach($generates as $key => $generate)
                                <div class="col-md-4 @if($generate->status=='NEW' || $generate->status=='IN_QUEUE' || $generate->status=='IN_PROGRESS') waiting @elseif($generate->status=='COMPLETED' || $generate->status=='ERROR') @endif generate_{{$generate->id}}" data-id="{{ $generate->id }}">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                         <div class="row">
                                            <div class="col-md-12">
                                              <a href="{{ $generate->image_url ?? '' }}"> 
                                                <img src="{{ $generate->image_url ?? asset('images/loading.gif') }}" class="img-fluid image_{{$generate->id}}" alt="{{$generate->title}}">
                                             </a> 
                                            </div>
                                            <div class="col-md-12">
                                            <p class="card-text">
                                                <p> <strong>{{ $generate->train->title ?? '' }}</strong><br>
                                               <span class="small text-muted"> <strong>{{ trans('cruds.train.fields.file_size') }}:</strong> {{ $generate->train->file_size ?? '' }}<br>
                                                <strong>{{ trans('cruds.generate.fields.status') }}:</strong> <span id="status_{{$generate->id}}">{{ $generate->status ?? '' }}</span><br>
                                                <strong>{{ trans('cruds.generate.fields.credit') }}:</strong> {{ $generate->credit ?? '' }}</span>
                                            </p>
                                            @can('generate_delete')
                                                <form action="{{ route('frontend.generates.destroy', $generate->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-danger btn-xs" value="{{ trans('global.delete') }}"><i class="fas fa-ban"></i></button>
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
@endsection
@section('scripts')
@parent
<script>
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
                    console.log(response);
                    if (response.status === 'COMPLETED') {
                        $('.waiting.generate_' + generateId).removeClass('waiting');
                        $('.image_' + generateId).attr('src', response.images[0].url);
                        $('#status_' + generateId).text('COMPLETED');
                    } else if (response.status === 'ERROR') {
                        $('.waiting.generate_' + generateId).removeClass('waiting').addClass('error');
                        $('#status_' + generateId).text('ERROR');
                    }
                },
                error: function() {
                    console.log('Error fetching status for generate ID:', generateId);
                }
            });
        });
    }, 5000);
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