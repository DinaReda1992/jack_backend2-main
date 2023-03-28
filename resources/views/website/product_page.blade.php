@extends('website.layout')
@php
    $product = json_decode($product);
    $relatedProducts = json_decode($relatedProducts);
@endphp
@section('title')
    <title> {{ $product->title }} </title>
    <meta name="description" content="{{ $product->description }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@stop
@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body"
                                href="/">{{ __('dashboard.home') }}</a>
                        </li>
                        <li class="breadcrumb-item pl-0 d-flex align-items-center"><a class="text-decoration-none text-body"
                                href="/search?category_id={{ @$product->category_id }}">{{ @$product->category_name }}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                            {{ @$product->title }}</li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="pt-11 pb-9 pb-lg-13 product-details-layout-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 pl-xl-9 mb-8 mb-md-0 primary-gallery summary-sticky" id="summary-sticky">
                        <div class="primary-summary-inner">
                            <div class="galleries-product galleries-product-02">
                                <div class="slick-slider slider-for custom-dots-01 mx-0"
                                    data-slick-options='{"slidesToShow": 1, "autoplay":false,"dots":true,"arrows":false}'>
                                    <div class="box px-0">
                                        <div class="card p-0 rounded-0 border-0">
                                            <a href={{ $product->photo }} class="card-img" data-gtf-mfp="true"
                                                data-gallery-id="02">
                                                <img src={{ $product->photo }} alt="{{ $product->title }}" class="w-100"
                                                    style="height: 400px;">
                                            </a>
                                        </div>
                                    </div>
                                    @foreach ($product->photos as $photo)
                                        <div class="box px-0">
                                            <div class="card p-0 rounded-0 border-0">
                                                <a href={{ $photo->photo }} class="card-img" data-gtf-mfp="true"
                                                    data-gallery-id="02">
                                                    <img src={{ $photo->photo }} alt="{{ $product->title }}"
                                                        class="w-100" style="height: 400px;">
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 pro-details">
                        <h2 class="fs-24 mb-2">{{ $product->title }}</h2>
                        <p class="d-flex align-items-center mb-3">
                            @if ($product->offer_price > 0)
                                <span dir="rtl"
                                    class="fs-18 text-secondary font-weight-bold ml-3">{{ $product->offer_price }}
                                    {{ __('dashboard.sar') }}</span>
                                <span class="text-line-through" dir="rtl">{{ $product->price }}
                                    {{ __('dashboard.sar') }}</span>
                            @else
                                <span dir="rtl"
                                    class="fs-18 text-secondary font-weight-bold ml-3">{{ $product->price }}
                                    {{ __('dashboard.sar') }}</span>
                            @endif
                            @if ($product->offer_type != '')
                                <span class="badge badge-primary fs-16 ml-4  font-weight-600 py-2 px-3"
                                    style="margin-right: 15px">{{ $product->offer_type }}</span>
                            @endif
                        </p>
                        <p class="mb-3">{{ $product->description }} </p>
                        <add-to-cart-action :item="{{ json_encode($product) }}" :single="2"
                            :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}"></add-to-cart-action>
                        <div class="d-flex align-items-center flex-wrap mt-4 mb-4">
                            <add-to-wishlist :item="{{ json_encode($product) }}" :single="1"
                                :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}"></add-to-wishlist>
                            <hr>
                        </div>
                        <ul class="accordion list-unstyled">
                            <li>
                                <a class="toggle active" href=#>
                                    <h5 class="mb-0 fs-20 w-100">
                                        <div class="d-flex align-items-center">
                                            <span>{{ __('dashboard.Product details') }} </span>
                                            <span class="iconn fa fa-plus ml-auto"></span>
                                            <span class="iconn fa fa-minus font-hide ml-auto"></span>
                                        </div>
                                    </h5>
                                </a>
                                <div class="inner show">
                                    <div class="card-body pt-5 pb-1 px-0">
                                        <p>{{ $product->description }}</p>
                                        <div class="row">
                                            <div class="col-6 col-md-3 text-center mb-6">
                                                <i class="far fa-refrigerator"></i>
                                                <br />
                                                <span>
                                                    {{ $product->deliver_status == 3 ? __('dashboard.It does not need to be refrigerated') : __('dashboard.It needs to be cooled') }}</span>
                                            </div>
                                            <div class="col-6 col-md-3 text-center mb-6">
                                                <i class="far fa-blanket"></i>
                                                <br />
                                                <span>
                                                    {{ $product->has_cover ? __('dashboard.A special bag is available') : __('dashboard.No special bag available') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <a class="toggle" href=#>
                                    <h5 class="mb-0 fs-20 w-100">
                                        <div class="d-flex align-items-center">
                                            <span>{{ __('dashboard.Product properties') }} </span>
                                            <span class="iconn fa fa-plus ml-auto"></span>
                                            <span class="iconn fa fa-minus font-hide ml-auto"></span>
                                        </div>
                                    </h5>
                                </a>
                                <div style="display:none" class="inner">
                                    <div class="card-body pt-5 pb-1 px-0">
                                        <div class="table-responsive mb-5">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td class="pl-0 text-secondary">
                                                            {{ __('dashboard.measurement_unit') }}</td>
                                                        <td class="text-right pr-0">{{ @$product->measurement_unit }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-0 text-secondary">{{ __('dashboard.expiry') }}</td>
                                                        <td class="text-right pr-0">
                                                            {{ $product->expiry }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-0 text-secondary">{{ __('dashboard.temperature') }}
                                                        </td>
                                                        <td class="text-right pr-0">{{ $product->temperature }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-0 text-secondary">
                                                            {{ __('dashboard.deliver_status') }}</td>
                                                        <td class="text-right pr-0">
                                                            {{ @$product->delivery_status }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-0 text-secondary">{{ __('dashboard.weight_unit') }}
                                                        </td>
                                                        <td class="text-right pr-0">{{ $product->weight }}
                                                            {{ __('trans.gm') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-0 text-secondary">
                                                            {{ __('dashboard.Do you provide a special bag for the product?') }}
                                                        </td>
                                                        <td class="text-right pr-0">
                                                            {{ $product->has_cover ? __('dashboard.Not always available') : __('dashboard.no') }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p>
                                            {{ $product->description }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
        </section>
        @if (count($relatedProducts) > 0)
            <section class="pt-10 pt-lg-12 pb-9 pb-lg-11 border-top">
                <div class="container">
                    <div class="d-flex justify-content-between">
                        <h3 class="text-center fs-34 mb-8">{{ __('trans.You may also like') }}</h3>
                        <i class="youmaylike text-center fs-34 mb-8 fas fa-arrow-left"></i>
                    </div>
                    <div class="slick-slider mx-n2"
                        data-slick-options='{"slidesToShow": 4,"dots":true,"arrows":false,"responsive":[{"breakpoint": 1368,"settings": {"arrows":false,"dots":true}},{"breakpoint": 1200,"settings": {"slidesToShow":3,"arrows":false,"dots":true}},{"breakpoint": 992,"settings": {"slidesToShow":2,"arrows":false,"dots":true}},{"breakpoint": 768,"settings": {"slidesToShow": 2,"arrows":false,"dots":true}},{"breakpoint": 576,"settings": {"slidesToShow": 1,"arrows":false,"dots":true}}]}'>
                        @foreach ($relatedProducts as $rlProduct)
                            {!! View::make('items.product')->with('product', $rlProduct)->render() !!}
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

@endsection
