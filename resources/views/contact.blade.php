@extends('layouts.layout ')
@section('title')
    <title>  الرئيسية </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')
    <!-- start products of category section -->
    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul class="list-inline">
                            <li>
                                <a href="/">الرئيسية</a>
                            </li>
                            <li>/</li>
                            <li>اتصل بنا </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-boxes">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="con-box text-center">
                        <h3>
                            <i class="la la-envelope"></i>
                        </h3>
                        <a href="mailto:({{$email}})"><h5>{{$email}}</h5></a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="con-box text-center">
                        <h3>
                            <i class="la la-whatsapp"></i>
                        </h3>
                       <a href="https://api.whatsapp.com/send?phone={{$whatsapp}}" target="_blank"><h5>{{$whatsapp}}</h5></a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="con-box text-center">
                        <h3>
                            <i class="la la-map-marker"></i>
                        </h3>
                        <h5>
                            {{$address}}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-us">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="contact-img">
                        <img src="https://www.oceanacross.com.sa/wp-content/uploads/2021/01/contact-us-figure.png" alt="img" />
                    </div>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <div class="ser-head">
                        <h3>لديك استشاره معينة</h3>
                        <p>
                            قم بارسال رسالتك وسيتم الرد عليكم فى أسرع وقت ممكن
                        </p>
                    </div>
                    <div class="con-form">
                        <form>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="d" placeholder="الاسم الاول">
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="dd" placeholder="الاسم الثاني">
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <input type="mail" class="form-control" id="ddd" placeholder="البريد الاكتروني ">
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="dddd" placeholder="رقم الجوال">
                                </div>
                                <div class="form-group col-md-12">
                                    <textarea class="form-control" rows="8" placeholder="الموضوع"></textarea>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <button type="submit" class="send">ارسال</button>
                                </div>
                            </div>


                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- end products of category section -->

@endsection
