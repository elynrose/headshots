@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.show') }} {{ trans('cruds.generate.title') }}
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.generates.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $generate->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.prompt') }}
                                    </th>
                                    <td>
                                        {{ $generate->prompt }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.train') }}
                                    </th>
                                    <td>
                                        {{ $generate->train->title ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.width') }}
                                    </th>
                                    <td>
                                        {{ $generate->width }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.height') }}
                                    </th>
                                    <td>
                                        {{ $generate->height }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.status') }}
                                    </th>
                                    <td>
                                        {{ $generate->status }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.image_url') }}
                                    </th>
                                    <td>
                                        {{ $generate->image_url }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.content_type') }}
                                    </th>
                                    <td>
                                        {{ $generate->content_type }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.inference') }}
                                    </th>
                                    <td>
                                        {{ $generate->inference }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.seed') }}
                                    </th>
                                    <td>
                                        {{ $generate->seed }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.credit') }}
                                    </th>
                                    <td>
                                        {{ $generate->credit }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.generate.fields.user') }}
                                    </th>
                                    <td>
                                        {{ $generate->user->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.generates.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection