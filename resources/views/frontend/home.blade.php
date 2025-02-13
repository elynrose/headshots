@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="d-flex flex-wrap">
                        @foreach($fals as $key => $fal)
                            <div class="m-2" style="width: 18rem;">
                                    <p class="card-title"><a href="{{ route('frontend.generates.createWithModel', $fal->id) }}">{{ $fal->title }}</a></p>
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