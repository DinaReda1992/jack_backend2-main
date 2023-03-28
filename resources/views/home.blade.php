@extends('layouts.layout ')
@section('title')
<title>  الرئيسية </title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')
<!-- start banner section -->
<section class="home-banner">
    <div class="banner-carousel owl-carousel owl-theme">
        @foreach($sliders as $slider)
        <div class="item">
            <a class="d-block" href="{{$slider->button_url}}">
            <img src="/uploads/{{$slider->photo}}" class="d-block w-100" alt="...">
            </a>

{{--            <div class="carousel-caption d-block d-md-block">--}}
{{--                <div class="banner-text">--}}
{{--                    <h1>{{$slider->title}}</h1>--}}
{{--                    <h3>{{$slider->description}}</h3>--}}
{{--                    @if($slider->button_title)--}}
{{--                    <a class="btn hvr-sweep-to-left" href="{{$slider->button_url}}">--}}
{{--                        {{$slider->button_title}}--}}
{{--                    </a>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        @endforeach
    </div>
</section>
<!-- end banner section -->
<!-- start new products section -->
<section class="new-products">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="section-title">
                    <div class="section-name">
                        <img src="/site/imgs/new.svg" alt="new icon">
                        أحدث المنتجات
                    </div>
                    <a href="/search" class="section-more">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="col-md-12 col-12">
                <div class="first-home owl-carousel owl-theme ">
                    @foreach($last_products as $lsProduct)
                        {!! View::make("items.product") -> with('product',$lsProduct) -> render() !!}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end new products section -->
<!-- start ad banner section -->
<section class="add-banner">
    <div class="container">
        <div class="row">
            <div class="col-md-1 d-md-block d-sm-none d-none"></div>
            <div class="col-md-10 col-12">
                <a href="#">
                    <div>
                        @php
                            $middle_photo = \App\Models\Settings::where('option_name', 'middle_photo')
->first();

                        @endphp
                        <img src="/uploads/{{isset($middle_photo)?$middle_photo->value:''}}" alt="ad banner">
                    </div>
                </a>
            </div>
            <div class="col-md-1 d-md-block d-sm-none d-none"></div>
        </div>
    </div>
</section>
<!-- end ad banner section -->
<!-- start suppliers section -->
<section class="home-suppliers">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="section-title">
                    <div class="section-name">
                        <img src="/site/imgs/supplier.svg" alt="new icon">
                        الموردين
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="owl-filter-bar">
                    <a class="item active" data-owl-filter="*">الكل</a>
                    @foreach($supplier_categories as $spCategory)
                    <a class="item" data-owl-filter=".sCategory{{$spCategory->id}}">{{$spCategory->name}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="second-home owl-carousel owl-theme">
                    @foreach($suppliers as $supplier)
                        @php
                                $rows='';
foreach ($supplier->supplier_categories as $row)
                        {
$rows.=' sCategory'.$row->category_id;
                        }
                                @endphp
                    <div class="item {{$rows}}">
                        <a href="/provider-products/{{$supplier->id}}">
                            <img src="/uploads/{{$supplier->supplier->photo}}" alt="{{$supplier->supplier->supplier_name}}">
                            <div class="back">
                                <div class="supplier-text">
                                    <h3 class="supplier-name">
                                        {{$supplier->supplier->supplier_name}}
                                    </h3>
                                    <p class="product-count">
                                        {{$supplier->products->count()}} Product
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <div class="carousel-btn">
                    <a class="btn hvr-sweep-to-left" href="/providers">
                        عرض الكل
                    </a>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- end suppliers section -->
<!-- start products section -->
<section class="products">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="section-title">
                    <div class="section-name">
                        <img src="/site/imgs/supplier.svg" alt="new icon">
                        المنتجات
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="owl-filter-bar2">
                    <a class="item active" data-owl-filter="*">الكل</a>
                    @foreach($product_categories as $prCategory)
                    <a class="item" data-owl-filter=".prCategory{{$prCategory->id}}">{{$prCategory->name}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="third-home owl-carousel owl-theme product-pice">
                    @foreach($products as $product)
                    {!! View::make("items.product") -> with('product',$product) -> render() !!}
                    @endforeach
                </div>
                <div class="carousel-btn">
                    <a class="btn hvr-sweep-to-left" href="/search">
                        عرض الكل
                    </a>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- end products section -->
<a class="install-app" href="{{url('/getApp')}}">
    <div class="install-con">
        <img src="/images/close.svg" alt="close" class="close close-pop-up">
        <img src="/images/logo.png" alt="icon" class="golden-logo">
        <div class="content">
            <h3 class="text-dark">تطبيق قولدن</h3><p class="text-dark">حمل تطبيق قولدن على جوالك </p>
            <button class="btn main-btn" >تحميل التطبيق</button>
        </div>
    </div>
</a>
    @endsection
