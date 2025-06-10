@extends('layouts.photo-upload')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="text-lg font-medium text-gray-900">Upload Photos</h3>
                    <p class="mt-1 text-sm text-gray-500">Upload your photos to use for training or generation.</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.photos.store") }}" enctype="multipart/form-data" id="photo-upload-form">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label class="required block text-sm font-medium text-gray-700 mb-2" for="photo">
                                {{ trans('cruds.photo.fields.photo') }}
                            </label>
                            <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" 
                                 id="photo-dropzone"
                                 x-data="{ 
                                    hasFiles: false,
                                    maxFiles: 10,
                                    currentFiles: 0,
                                    init() {
                                        this.$watch('currentFiles', (value) => {
                                            this.hasFiles = value > 0;
                                        });
                                    }
                                 }">
                                <div class="dz-message" data-dz-message>
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-500">
                                            Drag and drop your photos here or click to browse
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Maximum file size: 2MB. Supported formats: JPG, PNG, GIF
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if($errors->has('photo'))
                                <div class="mt-2 text-sm text-red-600">
                                    {{ $errors->first('photo') }}
                                </div>
                            @endif
                            <div class="mt-2 text-sm text-gray-500">
                                {{ trans('cruds.photo.fields.photo_helper') }}
                            </div>
                        </div>

                        <div class="form-group mt-6">
                            <div class="flex items-center">
                                <input type="hidden" name="use_for_training" value="0">
                                <input type="checkbox" 
                                       name="use_for_training" 
                                       id="use_for_training" 
                                       value="1" 
                                       {{ old('use_for_training', 0) == 1 ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="use_for_training" class="ml-2 block text-sm text-gray-900">
                                    {{ trans('cruds.photo.fields.use_for_training') }}
                                </label>
                            </div>
                            @if($errors->has('use_for_training'))
                                <div class="mt-2 text-sm text-red-600">
                                    {{ $errors->first('use_for_training') }}
                                </div>
                            @endif
                            <div class="mt-1 text-sm text-gray-500">
                                {{ trans('cruds.photo.fields.use_for_training_helper') }}
                            </div>
                        </div>
                       
                        <div class="form-group mt-6">
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    :disabled="!hasFiles">
                                <i class="fas fa-upload mr-2"></i>
                                {{ trans('global.upload') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var uploadedPhotoMap = {}
Dropzone.options.photoDropzone = {
    url: '{{ route('frontend.photos.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
        maxFiles: 10,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
        init: function() {
            this.on("addedfile", function(file) {
                // Update the current files count
                this.options.currentFiles = this.files.length;
            });
            this.on("removedfile", function(file) {
                // Update the current files count
                this.options.currentFiles = this.files.length;
            });
            this.on("success", function(file, response) {
      $('form').append('<input type="hidden" name="photo[]" value="' + response.name + '">')
      uploadedPhotoMap[file.name] = response.name
            });
            this.on("removedfile", function(file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedPhotoMap[file.name]
      }
      $('form').find('input[name="photo[]"][value="' + name + '"]').remove()
            });
            this.on("error", function(file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }
         return _results
            });
     }
}
</script>
@endsection