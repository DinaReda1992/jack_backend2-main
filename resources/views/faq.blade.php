@extends('layout ')
@section('title')
    <title>  الرئيسية - الاسئلة الشائعة </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop

@section('content')
    <!-- ============================ Page Title Start================================== -->
    <div class="image-cover page-title" style="background:url(/site/images/a.jpg) no-repeat;" data-overlay="6">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">

                    <h2 class="ipt-title"> الأسئلة الشائعة</h2>

                </div>
                <div class="col-lg-6 col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active" aria-current="page">الأسئلة الشائعة</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================ Page Title End ================================== -->

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