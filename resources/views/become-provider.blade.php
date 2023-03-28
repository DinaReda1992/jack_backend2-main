@extends('layouts.layout ')
@section('title')
<title> انضم كمورد </title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')
    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul>
                            <li>
                                <a href="/">الرئيسية</a>
                            </li>
                            <li>/</li>
                            <li>انضم كمورد</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="login-section">
        <div class="">
            <div >
                <becomeprovider :countries="{{$countries}}" />
            </div>
        </div>
    </section>


@endsection
