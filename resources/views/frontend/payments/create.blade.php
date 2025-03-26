@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

    
    <!-- Pricing Section -->
    <div class="row justify-content-center py-5">
        <div class="col-md-12">
            <h2 class="text-center mb-5">Pricing</h2>
            <div class="row">
               <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="card border-dark bg-dark text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">Popular Package</h5>
                            <ul class="list-unstyled">
                                <li>100 Credits</li>
                                <li>2 Training</li>
                                <li>20 Photos</li>
                            </ul>
                            <h1 class="card-text mt-5 mb-3">$15</h1>
                            <p><a href="/credits/basic/" class="btn btn-dark">Buy Credits</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>

    </div>
</div>
@endsection