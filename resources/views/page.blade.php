@extends('layouts.layout ')
@section('title')
<title>{{ $object->meta_title?:\App\Models\Settings::find(2)->value }} - الرئيسية </title>
<meta name="description" content="{{$object->meta_description?: \App\Models\Settings::find(3)->value }}">
<meta name="keywords" content="{{ $object->meta_keywords?:\App\Models\Settings::find(4)->value }}">
@stop
@section('content')
    <!-- ============================ Page Title Start================================== -->
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
                            <li>{{$object->page_name}} </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================ Our Story Start ================================== -->
    <section class="about-p">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="about-info">
                        {!! $object->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>



@stop