@extends('layout-app ')

@section('content')

    <!-- ================= Our Mission ================= -->
    <section>
        <div class="container">

            <div class="row">

@foreach($objects as $faq)
                    <div class="col-lg-6 col-md-6 col-sm-12">

                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h6 class="mb-0" data-toggle="collapse" data-target="#collapse{{$faq->id}}" aria-expanded="true"
                                aria-controls="collapseOne">
                                {{$faq->question}}
                            </h6>
                        </div>

                        <div id="collapse{{$faq->id}}" class="collapse " aria-labelledby="headingOne"
                             data-parent="#generalac">
                            <div class="card-body">
                                <p class="ac-para">
{{$faq->answer}}
                                </p>
                            </div>
                        </div>
                    </div>
                    </div>

                @endforeach

            </div>
        </div>
    </section>
    <!-- ================= Our Mission ================= -->
@stop