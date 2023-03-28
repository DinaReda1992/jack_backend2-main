@extends('layouts.layout ')
@section('title')
    <title> الموردين </title>
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
                            <li class="d-inline-block">الموردين</li>
                            <li class="d-inline-block">/</li>
                            <li class="d-inline-block">{{$supplier->supplier->supplier_name}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="services-page">
        <div class="container">
            <products
                    :supplier="{{$supplier}}"
                    @if(auth()->check())
                    :user="{{auth()->user()}}"
                    @else
                        :user="{{@json_encode([
                        'id'=> 0,
                            'activate'=> 0,
                    ])}}"
                    @endif
            />
        </div>
    </section>

@endsection
