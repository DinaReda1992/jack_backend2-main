@extends('website.layout')
@section('title')
    <title> {{ __('dashboard.home') }} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@stop
@php
    $products = json_decode($products);
    $random_product = json_decode($random_product);
@endphp
@section('content')
    <main id="content">
        @if ($main_sliders->count() > 0)
            <section id="mainslider" class="mx-0 slick-slider dots-inner-center slider main-slick"
                 data-slick-options='{"slidesToShow": 1,"infinite":true,"autoplay":true,"dots":true,"arrows":false,"fade":true,"cssEase":"ease-in-out","speed":600}'>
                @foreach ($main_sliders as $slider)
                    <div class="box px-0">
                        <div class="container mainslider container-xl">
                            <div class="bg-img-cover-center pt-12 pb-13 pb-lg-16 pt-lg-15 pl-3 pl-lg-13"
                                style="background-image: url({{ $slider->photo }});">
                                <div class="pt-4 mb-6" data-animate="fadeInDown">
                                    <a href="{{ $slider->button_url }}">
                                        <h1 class="slidertitle mb-3 fs-46 fs-md-56 lh-128"
                                            style="color: {{ $slider->text_color }}">
                                            {{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}</h1>
                                    </a>
                                    <p class=" sliderdesc fs-18 lh-166" style="max-width: 454px;">
                                        {{ app()->getLocale() == 'en' ? $slider->description_en : $slider->description }}
                                    </p>
                                </div>
                                @php
                                    $button_title=app()->getLocale() == 'ar' ? $slider->button_title : $slider->button_title_en;
                                @endphp
                                @if ($button_title)
                                    <div class="mb-1" data-animate="fadeInUp">
                                        <a href="{{ $slider->button_url }}"
                                            class="btn btn-secondary rounded bg-hover-primary border-0">{{ $button_title }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif
        @if ($top_icon_sliders->count() > 0)
            <section>
                <div class="container container-xl my-5">
                    <div class="row">
                        @foreach ($top_icon_sliders as $slider)
                            <div class="col-md-4 mb-6 mb-md-0 px-xl-8">
                                <div class="card border-0 text-center" data-animate="fadeInUp">
                                    <div class="mw-102 mx-auto">
                                        <img src="{{ $slider->photo }}"
                                            alt="{{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}"
                                            style="height:100px;">
                                    </div>
                                    <div class="card-body px-0 pt-6 mt-1 pb-0">
                                        <h3 class="fs-24 mb-3">
                                            {{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}</h3>
                                        <p class="mb-0">
                                            {{ app()->getLocale() == 'en' ? $slider->description_en : $slider->description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @if ($top_sliders->count() > 0)
            <section class="pt-6">
                <div class="container container-xl">
                    <div class="row">
                        @foreach ($top_sliders as $slider)
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <div class="card border-0 hover-shine hover-zoom-in banner banner-02"
                                    data-animate="fadeInUp">
                                    <div class="card-img bg-img-cover-center"
                                        style="background-image: url({{ $slider->photo }});"></div>
                                    <div class="card-img-overlay d-inline-flex flex-column px-7 pt-7 pb-6">
                                        <a href="{{ $slider->button_url }}">
                                            <h3 class="card-title fs-34">
                                                {{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}</h3>
                                        </a>
                                        <p class="card-text fs-18 text-secondary font-weight-600">
                                            {{ app()->getLocale() == 'en' ? $slider->description_en : $slider->description }}
                                        </p>
                                        @if ($slider->button_title)
                                            <div class="mt-auto">
                                                <a href="{{ $slider->button_url }}"
                                                    class="btn btn-link bg-transparent hover-secondary border-0 p-0 fs-16 font-weight-600">
                                                    {{ $slider->button_title }}
                                                    <svg class="icon icon-arrow-right">
                                                        <use xlink:href="#icon-arrow-right"></use>
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @foreach ($page_categories as $category)
            <section id="mysection-bottom" class="pt-lg-13 pt-10">
                <div class="container container-xl">
                    <div class="row mb-md-6 mb-8">
                        <div class="col-md-6">
                            <h2 class="fs-34" data-animate="fadeInUp">{{ $category->name }}</h2>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <a href="/search" class="btn btn-link p-0 mt-2">{{ __('dashboard.shop_all_products') }}<i
                                    class="far fa-arrow-right pl-2 fs-13"></i></a>
                        </div>
                    </div>
                    <div class="slick-slider mx-n2 products-slick"
                    data-slick-options='{"slidesToShow": 5,"dots":true,"arrows":false,"responsive":[{"breakpoint": 1368,"settings": {"arrows":false,"dots":true}},{"breakpoint": 1200,"settings": {"slidesToShow":3,"arrows":false,"dots":true}},{"breakpoint": 992,"settings": {"slidesToShow":2,"arrows":false,"dots":true}},{"breakpoint": 768,"settings": {"slidesToShow": 1,"arrows":false,"dots":true}},{"breakpoint": 576,"settings": {"slidesToShow": 1,"arrows":false,"dots":true}}]}'>
                        @if ($category->is_offer)
                            @foreach ($products as $product)
                                {!! View::make('items.product')->with('product', $product)->render() !!}
                            @endforeach
                        @else
                            @if (@$category->subcategory)
                                @foreach (@$category->subcategory->products as $product)
                                    @php
                                        $product = json_decode(json_encode(App\Http\Resources\ProductResources::make($product)));
                                    @endphp
                                    {!! View::make('items.product')->with('product', $product)->render() !!}
                                @endforeach
                            @elseif (@$category->category)
                                @foreach (@$category->category->products as $product)
                                    @php
                                        $product = json_decode(json_encode(App\Http\Resources\ProductResources::make($product)));
                                    @endphp
                                    {!! View::make('items.product')->with('product', $product)->render() !!}
                                @endforeach
                            @else
                                @foreach (@$category->products as $product)
                                    @php
                                        $product = json_decode(json_encode(App\Http\Resources\ProductResources::make($product)));
                                    @endphp
                                    {!! View::make('items.product')->with('product', $product)->render() !!}
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </section>
        @endforeach
        @if ($bottom_sliders->count() > 0)
            <section id="mysection-one" class="pt-11 pb-md-7 pb-10 pb-lg-14">
                <div class="container container-xl">
                    <div class="row">
                        @foreach ($bottom_sliders as $slider)
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <div class="card border-0 hover-shine hover-zoom-in banner banner-02"
                                    data-animate="fadeInUp">
                                    <div class="card-img bg-img-cover-center"
                                        style="background-image: url({{ $slider->photo }});"></div>
                                    <div class="card-img-overlay d-inline-flex flex-column px-7 pt-7 pb-6">
                                        <a href="{{ $slider->button_url }}">
                                            <h3 class="card-title fs-34">
                                                {{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}</h3>
                                        </a>
                                        <p class="card-text fs-18 text-secondary font-weight-600">
                                            {{ app()->getLocale() == 'en' ? $slider->description_en : $slider->description }}
                                        </p>
                                        @if ($slider->button_title)
                                            <div class="mt-auto">
                                                <a href="{{ $slider->button_url }}"
                                                    class="btn btn-link bg-transparent hover-secondary border-0 p-0 fs-16 font-weight-600">
                                                    {{ $slider->button_title }}
                                                    <svg class="icon icon-arrow-right">
                                                        <use xlink:href="#icon-arrow-right"></use>
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @if ($bottom_icon_sliders->count() > 0)
            <section>
                <div class="container container-xl mymargin">
                    <div class="row">
                        @foreach ($bottom_icon_sliders as $slider)
                            <div class="col-md-4 mb-6 mb-md-0 px-xl-8">
                                <div class="card border-0 text-center" data-animate="fadeInUp">
                                    <div class="mw-102 mx-auto">
                                        <img src="{{ $slider->photo }}"
                                            alt="{{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}"
                                            style="height:100px;">
                                    </div>
                                    <div class="card-body px-0 pt-6 mt-1 pb-0">
                                        <h3 class="fs-24 mb-3">
                                            {{ app()->getLocale() == 'en' ? $slider->title_en : $slider->title }}</h3>
                                        <p class="mb-0">
                                            {{ app()->getLocale() == 'en' ? $slider->description_en : $slider->description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @if ($random_product && $random_product->offer_type_id != 0)
            <section class="pt-lg-13 pt-md-10 pt-7 pb-lg-12 pb-11">
                <div class="container container-xl">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-5">
                            @if ($random_product->photos)
                                <img src="{{ $random_product->photos ? $random_product->photos[0]->photo : '/images/placeholder.png' }}"
                                    alt="{{ $random_product->title }}" data-animate="fadeIn">
                            @endif
                        </div>
                        <div class="col-lg-4 col-7">
                            <img src="{{ $random_product->photo }}" alt="{{ $random_product->title }}"
                                data-animate="fadeIn">
                        </div>
                        <div class="offset-xl-1 col-xl-4 col-lg-5 col-12 pt-lg-0 pt-9">

                            <div
                                class="d-flex align-items-center fs-15 text-secondary text-uppercase font-weight-600 letter-spacing-01 mb-4">
                                <span class="badge badge-primary fs-15 font-weight-500 py-2 px-2 ml-2"
                                    dir="ltr">{{ $random_product->offer_type }}</span>
                            </div>
                            <h2 data-animate="fadeInUp">{{ $random_product->title }}</h2>
                            <p class="fs-18 mw-460 mb-4" data-animate="fadeInUp">{{ $random_product->description }} </p>
                            <div class="countdown d-flex mb-7 mx-n2 mx-sm-n4" data-countdown="true" dir="ltr"
                                data-countdown-end="{{ $random_product->offer_end_date }}" data-animate="fadeInUp">
                                <div class="countdown-item center px-2 px-sm-4">
                                    <span class="fs-40 fs-sm-48 lh-1 text-primary font-weight-600 day">02</span>
                                </div>
                                <div class="separate fs-30">:</div>
                                <div class="countdown-item text-center px-2 px-sm-4">
                                    <span class="fs-40 fs-sm-48 lh-1 text-primary font-weight-600 hour">24</span>
                                </div>
                                <div class="separate fs-30">:</div>
                                <div class="countdown-item text-center px-2 px-sm-4">
                                    <span class="fs-40 fs-sm-48 lh-1 text-primary font-weight-600 minute">15</span>
                                </div>
                                <div class="separate fs-30">:</div>
                                <div class="countdown-item text-center px-2 px-sm-4">
                                    <span class="fs-40 fs-sm-48 lh-1 text-primary font-weight-600 second">41</span>
                                </div>
                            </div>
                            <a href="/product/{{ $random_product->id }}"
                                class="btn btn-secondary bg-hover-primary border-hover-primary" data-animate="fadeInUp">
                                {{ $random_product->offer_price > 0 ? $random_product->offer_price : $random_product->price }}
                                {{ __('dashboard.sar') }} </a>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection
