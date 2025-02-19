@extends('layouts.frontend')
@section('content')
<div class="container">
<h3 class="mb-5">Generate Image</h3>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('frontend.generates.store') }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf

                        @if($fals->model_type == 'image' || $fals->model_type == 'video')
                            <div class="form-group">
                                <label for="prompt">{{ trans('cruds.generate.fields.prompt') }}</label>
                                <textarea class="form-control" name="prompt" id="prompt">{{ old('prompt') }}</textarea>
                                @if($errors->has('prompt'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('prompt') }}
                                    </div>
                                @endif
                                <span class="help-block small text-muted">{{ trans('cruds.generate.fields.prompt_helper') }}</span>
                            </div>
                        @elseif($fals->model_type == 'audio')
                            <div class="form-group">
                                <input type="hidden" name="video_url" id="video_url" value="{{ $existingImages->video_url }}">
                                <label for="audio_mp3" class="required">Select Audio File</label>
                                <input type="file" name="audio_mp3" id="audio_mp3">
                            </div>
                        @endif

                        @if($fals->model_type == 'image')
                            <div class="form-group">
                                <label class="required" for="train_id">{{ trans('cruds.generate.fields.train') }}</label>
                                <select class="form-control select" name="train_id" id="train_id" required>
                                    @foreach($trains as $id => $entry)
                                        <option value="{{ $id }}" {{ old('train_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('train'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('train') }}
                                    </div>
                                @endif
                                <span class="help-block small text-muted">{{ trans('cruds.generate.fields.train_helper') }}</span>
                            </div>
                        @endif

                        @if($fals->model_type == 'video' && isset($existingImages))
                            @if(Request::get('parent_id'))
                                <input name="parent" type="hidden" value="{{ Request::get('parent_id') }}">
                            @endif
                            <div class="form-group">
                                <div id="image-preview" class="mt-3">
                                    <input type="hidden" name="image_url" id="image_url" value="{{ $existingImages->image_url }}">
                                    <img src="{{ $existingImages->image_url }}" alt="Selected Image" class="img-thumbnail" style="width:100%; display:block;">
                                </div>
                            </div>
                        @elseif($fals->model_type == 'audio' && isset($existingImages))
                            @if(Request::get('parent_id'))
                                <input name="parent" type="hidden" value="{{ Request::get('parent_id') }}">
                            @endif
                            <div class="form-group">
                                <div id="video-preview" class="mt-3">
                                    <video width="100%" controls>
                                        <source src="{{ $existingImages->video_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </div>
                        @endif

                        @if($fals->model_type == 'image')
                            <div class="form-group">
                                <a href="#advanced"  data-toggle="collapse">{{ trans('global.advanced_mode') }}</a>
                            </div>
                            <div id="advanced" class="collapse">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="width">{{ trans('cruds.generate.fields.width') }}</label>
                                            <select class="form-control" name="width" id="width">
                                                @foreach([512, 576, 640, 704, 768, 832, 896, 960, 1024] as $size)
                                                    <option value="{{ $size }}">{{ $size }}</option>
                                                @endforeach
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
                                            <select class="form-control" name="height" id="height">
                                                @foreach([512, 576, 640, 704, 768, 832, 896, 960, 1024] as $size)
                                                    <option value="{{ $size }}">{{ $size }}</option>
                                                @endforeach
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
                                    <input class="form-control" type="range" name="inference" id="inference" value="{{ old('inference', '28') }}" min="1" max="100" step="1" oninput="this.nextElementSibling.value = this.value">
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
                                    <input class="form-control" type="text" name="seed" id="seed" value="{{ old('seed', '') }}">
                                    @if($errors->has('seed'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('seed') }}
                                        </div>
                                    @endif
                                    <span class="help-block small text-muted">{{ trans('cruds.generate.fields.seed_helper') }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <input class="form-control" type="hidden" name="credit" id="credit" value="{{ old('credit', '1') }}" step="1">
                            <input type="hidden" name="status" id="status" value="NEW">
                            <input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() }}">
                            <input type="hidden" name="fal_model_id" id="model_id" value="@if($fals){{ $fals->id }}@endif">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>

    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/lamejs/1.2.0/lame.min.js"></script>
<script>
    /*
document.addEventListener('DOMContentLoaded', function () {
    let audioContext;
    let microphoneStream;
    let scriptProcessor;
    let audioData = [];
    let isRecording = false;
    let mediaStream;

    const recordBtn = document.getElementById('recordBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const stopBtn = document.getElementById('stopBtn');
    const playBtn = document.getElementById('playBtn');
    const audioPlayback = document.getElementById('audioPlayback');
    const hiddenAudioInput = document.getElementById('audio_mp3');

    recordBtn.addEventListener('click', startRecording);
    pauseBtn.addEventListener('click', pauseRecording);
    stopBtn.addEventListener('click', stopRecording);
    playBtn.addEventListener('click', playRecording);

    async function startRecording() {
        if (!navigator.mediaDevices.getUserMedia) {
            alert('Your browser does not support audio recording.');
            return;
        }
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            microphoneStream = audioContext.createMediaStreamSource(mediaStream);
            scriptProcessor = audioContext.createScriptProcessor(4096, 1, 1);
            microphoneStream.connect(scriptProcessor);
            scriptProcessor.connect(audioContext.destination);
            scriptProcessor.onaudioprocess = function(e) {
                if (!isRecording) return;
                let channelData = e.inputBuffer.getChannelData(0);
                audioData.push(new Float32Array(channelData));
            };
            isRecording = true;
            recordBtn.disabled = true;
            pauseBtn.disabled = false;
            stopBtn.disabled = false;
            playBtn.disabled = true;
            pauseBtn.textContent = 'Pause';
            audioData = [];
        } catch (err) {
            console.error('Error accessing microphone: ', err);
        }
    }

    function pauseRecording() {
        if (isRecording) {
            isRecording = false;
            pauseBtn.textContent = 'Resume';
        } else {
            isRecording = true;
            pauseBtn.textContent = 'Pause';
        }
    }

    function stopRecording() {
        isRecording = false;
        recordBtn.disabled = false;
        pauseBtn.disabled = true;
        stopBtn.disabled = true;
        playBtn.disabled = false;
        if (scriptProcessor) {
            scriptProcessor.disconnect();
        }
        if (microphoneStream) {
            microphoneStream.disconnect();
        }
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
        let bufferLength = audioData.reduce((sum, arr) => sum + arr.length, 0);
        let mergedBuffer = new Float32Array(bufferLength);
        let offset = 0;
        for (let i = 0; i < audioData.length; i++) {
            mergedBuffer.set(audioData[i], offset);
            offset += audioData[i].length;
        }
        let pcmData = floatTo16BitPCM(mergedBuffer);
        let mp3Blob = encodeMP3(pcmData, audioContext.sampleRate);
        let url = URL.createObjectURL(mp3Blob);
        audioPlayback.src = url;
        audioPlayback.style.display = 'block';

        let reader = new FileReader();
        reader.onloadend = function() {
            hiddenAudioInput.value = reader.result;
        };
        reader.readAsDataURL(mp3Blob);
    }

    function playRecording() {
        audioPlayback.play();
    }

    function floatTo16BitPCM(buffer) {
        let l = buffer.length;
        let buf = new Int16Array(l);
        for (let i = 0; i < l; i++) {
            let s = Math.max(-1, Math.min(1, buffer[i]));
            buf[i] = s < 0 ? s * 0x8000 : s * 0x7FFF;
        }
        return buf;
    }

    function encodeMP3(pcmData, sampleRate) {
        const mp3Encoder = new lamejs.Mp3Encoder(1, sampleRate, 128);
        const chunkSize = 1152;
        let mp3Data = [];
        for (let i = 0; i < pcmData.length; i += chunkSize) {
            let chunk = pcmData.subarray(i, i + chunkSize);
            let mp3buf = mp3Encoder.encodeBuffer(chunk);
            if (mp3buf.length > 0) {
                mp3Data.push(new Int8Array(mp3buf));
            }
        }
        let mp3buf = mp3Encoder.flush();
        if (mp3buf.length > 0) {
            mp3Data.push(new Int8Array(mp3buf));
        }
        return new Blob(mp3Data, { type: 'audio/mp3' });
    }
});*/
</script>
@endsection
