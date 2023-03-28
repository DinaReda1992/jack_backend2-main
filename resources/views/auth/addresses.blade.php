@extends('layouts.layout')
@section('title')
<title>{{ \App\Models\Settings::find(5)->value }} - العناوين</title>
{{--<meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">--}}
{{--<meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">--}}
@endsection
@section('content')

    <!-- ================= login================= -->

    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul class="d-inline-block">
                            <li class="d-inline-block">
                                <a href="/">الرئيسية</a>
                            </li>
                            <li class="d-inline-block">/</li>
                            <li class="d-inline-block">اضافة عناوين</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="login-section">
        <div class="">
            <div >
                <addresses :user="{{auth()->user()}}" :countries="{{$country}}"  :addresses="{{$addresses}}"/>
            </div>
        </div>
    </section>
    <!-- start footer section -->

    <!-- ================= end login ================= -->


@endsection
{{--@section('mix')
    <script src="{{ mix('js/app.js') }}"></script>
@endsection--}}
