@extends('layouts.frontend')
@section('content')
<div class="container">
<h3 class="mb-5">Credit History</h3>

    <div class="row justify-content-center">
        <div class="col-md-12">
  
            <div class="card">
          

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Credit">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.credit.fields.points') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.credit.fields.created_at') }}
                                    </th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $key => $credit)
                                    <tr data-entry-id="{{ $credit->id }}">
                                        <td>
                                            {{ $credit->points ?? '' }}
                                        </td>
                                        <td>
                                            {{ $credit->created_at ?? '' }}
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
@can('credit_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.credits.massDestroy') }}",
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
  let table = $('.datatable-Credit:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection