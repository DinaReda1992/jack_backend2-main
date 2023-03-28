@extends('layouts.layout ')
@section('title')
<title> البحث </title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')
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
                            <li class="d-inline-block">البحث</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="suppliers-page">
        <div class="container">
            <providers
                    :categories="{{$categories}}"
                    settings="/uploads/{{@\App\Models\Settings::find(39)->value}}"
                    @if(auth()->check())
                    :user="{{auth()->user()}}"
                    @endif
            />
        </div>
    </section>

@endsection
