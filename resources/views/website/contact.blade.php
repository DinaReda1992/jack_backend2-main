@extends('website.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(2)->value }} -{{__('dashboard.contact_us')}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
    <style>
        .contact-boxes {
            padding: 22px 0 50px
        }

        .con-box {
            padding: 40px;
            background-color: #f8f8f8;
            position: relative;
            transition: all .5s ease 0s
        }

        .con-box:hover {
            margin-top: -20px
        }

        .con-box:hover h3 {
            transform: rotate(0)
        }

        .con-box h3 {
            color: #d18332;
            font-size: 33px;
            transform: rotate(360deg);
            transition: all .5s ease 0s
        }

        .con-box h5 {
            color: #bababa
        }

        .contact-boxes .col-md-4:nth-child(3) .con-box h5 {
            line-height: 22px;
            font-size: 13px
        }

        .contact-us {
            padding-bottom: 35px
        }

        .contact-img {
            text-align: center
        }

        .contact-img img {
            width: 75%;
            height: 218px;
            margin-top: 120px;
            object-fit: contain
        }

        .contact-us .ser-head {
            margin-top: 35px
        }
    </style>
@stop
@section('content')
    <!-- start products of category section -->
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">{{__('dashboard.contact_us')}}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="pb-11 pb-lg-13 pt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="con-box text-center">
                            <h3>
                                <i class="fa fa-envelope"></i>
                            </h3>
                            <a href="mailto:({{ $email }})">
                                <h5>{{ $email }}</h5>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="con-box text-center">
                            <h3>
                                <i class="fab fa-whatsapp"></i>
                            </h3>
                            <a href="https://api.whatsapp.com/send?phone={{ $whatsapp }}" target="_blank">
                                <h5>{{ $whatsapp }}</h5>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="con-box text-center">
                            <h3>
                                <i class="fa fa-map-marker"></i>
                            </h3>
                            <h5>
                                {{ $address }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="pb-11 pb-lg-13">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="contact-img">
                            <img src="https://www.oceanacross.com.sa/wp-content/uploads/2021/01/contact-us-figure.png"
                                alt="img" />
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-12">
                        <div class="ser-head">
                            <h3>{{__('dashboard.you_have_specific_advice')}}</h3>
                            <p>
                                {{__('dashboard.Send your message and we will reply to you as soon as possible')}}
                            </p>
                        </div>
                        <div class="con-form">
                            <form method="post" action="/send-message-email">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" id="d" placeholder="{{__('dashboard.name')}}" name="name">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <input type="mail" class="form-control" id="ddd"
                                            placeholder="{{__('dashboard.email')}} " name="email">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" id="dddd" placeholder="{{__('dashboard.phone')}}" name="phone">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" id="dddd" placeholder="{{__('dashboard.subject')}}" name="subject">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea class="form-control" rows="8" placeholder="{{__('dashboard.message')}}" name="message"></textarea>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                        <button type="submit" class="btn btn-primary pt-1 pl-2 pb-1 pr-2">{{__('dashboard.send')}}</button>
                                    </div>
                                </div>


                            </form>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
