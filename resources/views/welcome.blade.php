@extends('layouts.frontend')

@section('content')
    <!-- Header Section -->
   <div class="container-fluid py-5"> <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-center" style="background-image: url({{ asset('images/space.jpg')}}); background-size: cover; height: 300px; background-color:#000; background-position:10px -100px; animation: scrollBackground 30s linear infinite;">
                    <h1 class="text-white text-center">Go everywhere without going anywhere!</h1>
                </div>
            </div>

            <style>
                @keyframes scrollBackground {
                    0% {
                        background-position: 10px -100px;
                    }
                    100% {
                        background-position: 10px -500px;
                    }
                }
            </style>
        </div>
    </div>
</div>
<div class="container">

    <!-- Intro Section -->
    <div class="row justify-content-center py-5">
        <div class="col-md-12">
                              <p class="text-center lead">Step into a world of endless possibilities with Glassfish.ai! Transform your photos using cutting-edge AI technology and place yourself in any scenario you can dream of. Experience the future of photography and let your imagination run wild. Discover what could be with Glassfish.ai today!</p>
              
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="row justify-content-center py-5">
        <div class="col-md-12">
     
                    <h2 class="text-center mb-5">Try Endless Possibilities</h2>
                    <div class="row">
                        <div class="col-md-4" style="transform: rotate(2deg)!important; border-radius:6px;">
                            <img src="{{ asset('images/driving.jpg')}}" class="img-fluid floating1" alt="Driving a race car">
                            <p class="text-muted  mt-2 px-2 text-center">Me driving a race car, what would that look like in reality?</p>
                        </div>
                        <div class="col-md-4" style="transform: rotate(-2deg)!important;">
                            <img src="{{ asset('images/professional.jpg')}}" class="img-fluid floating3" alt="Professional Photo">
                            <p class="text-muted  mt-2 px-2 text-center">Need a professional photo for my linkedin profile.</p>
                        </div>
                        <div class="col-md-4" style="transform: rotate(2deg)!important;">
                            <img src="{{ asset('images/cliff.jpg')}}" class="img-fluid floating2" alt="In Space">
                            <p class="text-muted  mt-2 px-2 text-center">How high can i possibly go?</p>
                        </div>
                    </div>

                    <style>
                        .floating1 {
                            animation: floating 3s ease-in-out infinite;
                        }
                        .floating2 {
                            animation: floating 4s ease-in-out infinite;
                        }
                        .floating3 {
                            animation: floating 2s ease-in-out infinite;
                        }

                        @keyframes floating {
                            0% {
                                transform: translateY(0);
                            }
                            50% {
                                transform: translateY(-10px);
                            }
                            100% {
                                transform: translateY(0);
                            }
                        }
                    </style>
            
        </div>
    </div>

    <!--FAQ accordion section-->
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <h2 class="text-center mb-5">FAQ</h2>
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How do I get started?
                            </a>
                        </h5>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            To get started, simply create an account and purchase credits. Upload your photos into your photo gallery and select the scenario you want to place yourself in. Our AI technology will do the rest!
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <a class="collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                How long does it take to process my photo?
                            </a>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            The processing time depends on the complexity of the scenario you choose. On average, it takes 5-10 minutes to process a photo.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingThree">
                        <h5 class="mb-0">
                            <a class="collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Can I use the photos for commercial purposes?
                            </a>
                        </h5>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                        <div class="card-body">
                            Yes, you can use the photos for commercial purposes. However, you are not allowed to
                            resell the photos or claim them as your own work.
                        </div>
                    </div>
                </div>
                <!--Add more possible questions here-->
                

            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <a href="/terms">Terms of Service</a> | <a href="/policy">Privacy Policy</a>
                    <p>&copy; 2023 Your Company. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection