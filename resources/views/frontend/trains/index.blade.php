@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('train_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.trains.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.train.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.train.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Train">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.requestid') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.train.fields.title') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.train.fields.status') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.train.fields.diffusers_lora_file') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.train.fields.config_file') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.train.fields.file_size') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.train.fields.error_log') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trains as $key => $train)
                                    <tr data-entry-id="{{ $train->id }}">
                                        <td>
                                            {{ $train->requestid ?? '' }}
                                        </td>
                                        <td>
                                            {{ $train->title ?? '' }}
                                        </td>
                                        <td>
                                            {{ $train->status ?? '' }}
                                        </td>
                                        <td>
                                            {{ $train->zipped_file_url ?? '' }}
                                        </td>
                                        <td>
                                            {{ $train->config_file ?? '' }}
                                        </td>
                                        <td>
                                            {{ $train->file_size ?? '' }}
                                        </td>
                                        <td>
                                            {{ $train->error_log ?? '' }}
                                        </td>
                                        <td>
                                            @can('train_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.trains.show', $train->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('train_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.trains.edit', $train->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('train_delete')
                                                <form action="{{ route('frontend.trains.destroy', $train->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                </form>
                                            @endcan

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
@can('train_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.trains.massDestroy') }}",
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
  let table = $('.datatable-Train:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection