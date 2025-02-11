@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card">
        

                <div class="card-body">
                
                        <div class="form-group">
                            <label for="prompt">{{ trans('cruds.generate.fields.prompt') }}</label>
                            <textarea class="form-control" name="prompt" id="prompt" disabled>{{ old('prompt', $generate->prompt) }}</textarea>
                            @if($errors->has('prompt'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('prompt') }}
                                </div>
                            @endif
                            <span class="help-block small text-muted">{{ trans('cruds.generate.fields.prompt_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="train_id">{{ trans('cruds.generate.fields.train') }}</label>
                            <select class="form-control select2" name="train_id" id="train_id" required disabled>
                                @foreach($trains as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('train_id') ? old('train_id') : $generate->train->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('train'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('train') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.generate.fields.train_helper') }}</span>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="width">{{ trans('cruds.generate.fields.width') }}</label>
                                    <select class="form-control" name="width" id="width" disabled>
                                        <option value="512" {{ old('width', $generate->width) == 512 ? 'selected' : '' }}>512</option>
                                        <option value="576" {{ old('width', $generate->width) == 576 ? 'selected' : '' }}>576</option>
                                        <option value="640" {{ old('width', $generate->width) == 640 ? 'selected' : '' }}>640</option>
                                        <option value="704" {{ old('width', $generate->width) == 704 ? 'selected' : '' }}>704</option>
                                        <option value="768" {{ old('width', $generate->width) == 768 ? 'selected' : '' }}>768</option>
                                        <option value="832" {{ old('width', $generate->width) == 832 ? 'selected' : '' }}>832</option>
                                        <option value="896" {{ old('width', $generate->width) == 896 ? 'selected' : '' }}>896</option>
                                        <option value="960" {{ old('width', $generate->width) == 960 ? 'selected' : '' }}>960</option>
                                        <option value="1024" {{ old('width', $generate->width) == 1024 ? 'selected' : '' }}>1024</option>
                                    </select>
                                    @if($errors->has('width'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('width') }}
                                        </div>
                                    @endif
                                    <span class="help-block small text-muted">{{ trans('cruds.generate.fields.width_helper') }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="height">{{ trans('cruds.generate.fields.height') }}</label>
                                    <select class="form-control" name="height" id="height" disabled>
                                        <option value="512" {{ old('height', $generate->height) == 512 ? 'selected' : '' }}>512</option>
                                        <option value="576" {{ old('height', $generate->height) == 576 ? 'selected' : '' }}>576</option>
                                        <option value="640" {{ old('height', $generate->height) == 640 ? 'selected' : '' }}>640</option>
                                        <option value="704" {{ old('height', $generate->height) == 704 ? 'selected' : '' }}>704</option>
                                        <option value="768" {{ old('height', $generate->height) == 768 ? 'selected' : '' }}>768</option>
                                        <option value="832" {{ old('height', $generate->height) == 832 ? 'selected' : '' }}>832</option>
                                        <option value="896" {{ old('height', $generate->height) == 896 ? 'selected' : '' }}>896</option>
                                        <option value="960" {{ old('height', $generate->height) == 960 ? 'selected' : '' }}>960</option>
                                        <option value="1024" {{ old('height', $generate->height) == 1024 ? 'selected' : '' }}>1024</option>
                                    </select>
                                    @if($errors->has('height'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('height') }}
                                        </div>
                                    @endif
                                    <span class="help-block small text-muted">{{ trans('cruds.generate.fields.height_helper') }}</span>
                                </div>
                            </div>
                        </div>
           
                        <div class="form-group">
                            <label for="inference">{{ trans('cruds.generate.fields.inference') }}</label>
                            <input class="form-control" type="range" name="inference" id="inference" value="{{ old('inference', '28') }}" min="1" max="100" step="1" oninput="this.nextElementSibling.value = this.value" disabled>
                            <output>28</output>
                            @if($errors->has('inference'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('inference') }}
                                </div>
                            @endif
                            <span class="help-block small text-muted">{{ trans('cruds.generate.fields.inference_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="seed">{{ trans('cruds.generate.fields.seed') }}</label>
                            <input class="form-control" type="text" name="seed" id="seed" value="{{ old('seed', $generate->seed) }}" disabled>
                            @if($errors->has('seed'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('seed') }}
                                </div>
                            @endif
                            <span class="help-block small text-muted">{{ trans('cruds.generate.fields.seed_helper') }}</span>
                        </div>
              
                   
                        <div class="form-group">
                        <input class="form-control" type="hidden" name="credit" id="credit" value="1" step="1">
                        <input name="user_id" id="user_id" type="hidden" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="id" id="genid" value="{{ Request::segment(2)}}">
                         
                        </div>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body" id="imageDiv">
                    <h5>Image</h5>
                    <img src="{{ $generate->image_url }}" alt="Generated Image" style="max-width: 100%;">
                </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const genId = document.getElementById('genid').value;
        const userId = document.getElementById('user_id').value;
        const imageDiv = document.getElementById('imageDiv');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route('frontend.generate.status') }}',
            type: 'POST',
            data: {
                id: genId,
                user_id: userId
            },
            success: function(response) {
                console.log(response);
                if (response.status === 'SUCCESS') {
                    imageDiv.innerHTML = `
                        <h5>Image</h5>
                        <img src="${response.image_url}" alt="Generated Image" style="max-width: 100%;">
                    `;
                } else {
                    imageDiv.innerHTML = '<p>Image generation failed.</p>';
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching image:', error);
                imageDiv.innerHTML = '<p>Error fetching image.</p>';
            }
        });
    });

</script>
@endsection