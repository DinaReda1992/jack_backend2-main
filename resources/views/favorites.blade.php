@extends('layouts.layout ')
@section('title')
    <title> المفضلة </title>
    <meta name="description" content="">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')
    <!-- start products of category section -->
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
                            <li>المفضلة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="shopproduct my-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="title">
                        <h2>
                            المفضلة
                        </h2>
                    </div>
                </div>
                @foreach($favoriteProducts as $fProduct)
                    <div class="col-md-3 col-6">

                    {!! View::make("items.product") -> with('product',$fProduct) -> render() !!}
                    </div>
                @endforeach
                <div class="col-md-12 text-center mt-2 mt-md-5">

                    <nav aria-label="Page navigation example">
                        {!! $favoriteProducts->render() !!}
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end products of category section -->

@endsection
