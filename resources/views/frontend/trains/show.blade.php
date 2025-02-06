@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.show') }} {{ trans('cruds.train.title') }}
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.trains.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.requestid') }}
                                    </th>
                                    <td>
                                        {{ $train->requestid }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $train->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.status') }}
                                    </th>
                                    <td>
                                        {{ $train->status }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.diffusers_lora_file') }}
                                    </th>
                                    <td>
                                        {{ $train->diffusers_lora_file }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.config_file') }}
                                    </th>
                                    <td>
                                        {{ $train->config_file }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.file_size') }}
                                    </th>
                                    <td>
                                        {{ $train->file_size }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.error_log') }}
                                    </th>
                                    <td>
                                        {{ $train->error_log }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.train.fields.user') }}
                                    </th>
                                    <td>
                                        {{ $train->user->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.trains.index') }}">
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