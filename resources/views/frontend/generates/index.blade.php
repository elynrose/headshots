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
                <div class="card-header">
                    {{ trans('cruds.generate.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Generate">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.prompt') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.generate.fields.train') }}
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
                                        {{ trans('cruds.train.fields.requestid') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.generate.fields.status') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.generate.fields.image_url') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.generate.fields.credit') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.generate.fields.user') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generates as $key => $generate)
                                    <tr data-entry-id="{{ $generate->id }}">
                                        <td>
                                            {{ $generate->prompt ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->train->title ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->train->status ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->train->diffusers_lora_file ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->train->config_file ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->train->file_size ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->train->requestid ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->status ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->image_url ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->credit ?? '' }}
                                        </td>
                                        <td>
                                            {{ $generate->user->name ?? '' }}
                                        </td>
                                        <td>
                                            @can('generate_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.generates.show', $generate->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('generate_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.generates.edit', $generate->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('generate_delete')
                                                <form action="{{ route('frontend.generates.destroy', $generate->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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