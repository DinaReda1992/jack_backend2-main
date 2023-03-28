@extends('layouts.layout ')
@section('title')
    <title> {{ $product->title }} </title>
    <meta name="description" content="{{ $product->description }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@stop
@section('content')
    <!-- start product info section -->
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
                            <li class="d-inline-block">{{ @$product->category->name }} </li>
                            <li class="d-inline-block">/</li>
                            <li class="d-inline-block">{{ $product->title }} </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product-info">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="title">
                        {{ $product->title }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fotorama" data-nav="thumbs">
                        @foreach ($product->photos as $photo)
                            <a href="/uploads/{{ $photo->photo }}"><img src="/uploads/{{ $photo->photo }}"
                                    width="100"></a>
                        @endforeach
                    </div>


                    <div id="lightgallery" data-nav="thumbs">
                        @foreach ($product->photos as $photo)
                            <a href="/uploads/{{ $photo->photo }}"><img src="/uploads/{{ $photo->photo }}"
                                    width="100"></a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="product-rate">
                                    {{-- <label>تم بيعه ({{$product->cart_items_count}}) مرة</label> --}}

                                    {{-- <input class="rating rating-loading input-id" data-min="0" data-max="5" data-step="1" --}}
                                    {{-- value="{{$product->product_rate}}" data-size="xxs" disabled> --}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="first-info">
                                    @auth
                                        <div style="direction: ltr;display: inline-block" class="product-price">
                                            {{-- <small> 96.00 SAR</small> --}}
                                            {{ round($product->price, 2) }} SAR
                                        </div>

                                        {{-- <div class="product-count"> --}}
                                        {{-- المتبقي: {{$product->quantity}}  {{@$product->measurement->name}} --}}
                                        {{-- </div> --}}

                                        <div class="min-order">
                                            الحد الأدنى لكمية المنتج "{{ $product->title }}" يساوي
                                            {{ $product->min_quantity }}.
                                        </div>
                                    @endauth


                                    @if (auth()->user())
                                        <div class="product-bottom-details">
                                            <actions :item="{{ $product }}" :single="1"
                                                :user="{{ auth()->user() }}" />

                                            <!--                                        <div class="product-links">
                                                            <a href="#" data-wenk="مشاركة المنتج" data-wenk-pos="bottom"><i
                                                                        class="las la-share-alt"></i></a>
                                                            <a href="#" data-wenk="الاضافة الى المفضلة" data-wenk-pos="bottom"><i
                                                                        class="la la-heart"></i></a>
                                                            <a href="#" data-wenk="الاضافة الى السلة" data-wenk-pos="bottom"><i
                                                                        class="la la-shopping-cart"></i></a>
                                                            <a href="#" data-wenk="الاضافة الى المقارنة" data-wenk-pos="bottom"><i
                                                                        class="lar la-chart-bar"></i></a>
                                                        </div>-->
                                        </div>
                                    @else
                                        <div class="log-button">
                                            <a class="btn hvr-sweep-to-left" href="/login">
                                                يرجى تسجيل الدخول للاضافة للسلة
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="second-info">
                                    <div class="seller-info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <img height="100"
                                                    src="/uploads/{{ @$supplier->photo ?: 'default-img.png' }}?size=338&ext=jpg"
                                                    alt="seller logo">
                                            </div>
                                            <div class="col-md-8">
                                                <ul class="p-2">
                                                    <li>
                                                        المورد :
                                                        <a href="#">{{ @$supplier->supplier_name }}</a>
                                                    </li>
                                                    <li>
                                                        {{ \Illuminate\Support\Str::limit($supplier->bio, 70, $end = '...') }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sub-info">
                                        <div>
                                            <span class="product-feature-label">
                                                <em>هامش الربح</em>
                                            </span>
                                            <span>
                                                @php
                                                    ini_set('serialize_precision', -1);
                                                    $margin = $product->client_price - $product->price;
                                                    $profit_margin = ($margin / $product->price) * 100;
                                                @endphp
                                                <em>{{ @round($profit_margin, 2) }} %</em>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="product-feature-label">
                                                <em>وحدة البيع</em>
                                            </span>
                                            <span>
                                                <em>{{ @$product->measurement->name }}</em>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="product-feature-label">
                                                <em>مدة الصلاحية</em>
                                            </span>
                                            <span>
                                                <em>{{ $product->expiry }}</em>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="product-feature-label">
                                                <em>درجة الحرارة</em>
                                            </span>
                                            <span>
                                                <em>{{ $product->temperature }}</em>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="product-feature-label">
                                                <em>حالة التوصيل</em>
                                            </span>
                                            <span>
                                                <em>{{ @$product->deliverStatus->name }}</em>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="product-feature-label">
                                                <em>الوزن</em>
                                            </span>
                                            <span>
                                                <em>{{ $product->weight }} جرام </em>
                                            </span>
                                        </div>
                                        <!--                                    <div>
                                                        <span class="product-feature-label">
                                                            <em>الكمية المتوفرة</em>
                                                        </span>
                                                        <span>
                                                            <em>{{ $product->quantity }} جرام </em>
                                                        </span>
                                                    </div>-->
                                        <div>
                                            <span class="product-feature-label">
                                                <em>هل توفر كيس خاص</em>
                                            </span>
                                            <span>
                                                <em>{{ $product->has_cover ? 'ليس متوفر دائما ' : 'لا' }} </em>
                                            </span>
                                        </div>
                                        @if (auth()->user())
                                            <div>
                                                <span class="product-feature-label">
                                                    <em>السعر للعملاء</em>
                                                </span>
                                                <span>
                                                    <em>{{ $product->client_price }} ريال </em>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="info-box" style="margin-top: 10px;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="section-title" style="margin: 10px;">
                                    <div class="section-name">
                                        التفاصيل
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <p style="padding: 18px;">
                                    {{ $product->description }}
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end product info section -->
    <!-- start product info + rate section -->
    <section class="product-rate">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                aria-selected="true">المواصفات</button>
                            {{-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">المراجعات</button> --}}
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <div style="font-size: 1em" class="title">
                                مواصفات المنتج
                            </div>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">صلاحية المنتج :</th>
                                        <td>{{ $product->expiry }}</td>
                                    </tr>
                                    @auth
                                        <tr>
                                            <th scope="row">سعر التجزئة المقترح:</th>
                                            <td>{{ $product->client_price }}ريال </td>
                                        </tr>
                                    @endauth
                                    <tr>
                                        <th scope="row">وحدة البيع:</th>
                                        <td>{{ @$product->measurement->name }}</td>
                                    </tr>
                                    <!--                            <tr>
                                                <th scope="row">الكمية المتاحة:</th>
                                                <td>{{ $product->quantity }}</td>
                                            </tr>-->
                                    <tr>
                                        <th scope="row">وزن العلبة:</th>
                                        <td>{{ $product->weight }}g</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"> --}}
                        {{-- <section class="ser-evauate"> --}}
                        {{-- <div class="feed-back"> --}}
                        {{-- <ul class="list-unstyled"> --}}
                        {{-- @foreach ($ratings as $rate) --}}
                        {{-- <li> --}}
                        {{-- <div class="row"> --}}
                        {{-- <div class="col-md-8 col-sm-8 col-xs-8"> --}}
                        {{-- <div class="comm-name"> --}}
                        {{-- <h4>{{@$rate->user->username}}</h4> --}}
                        {{-- <h5>{{$rate->comment}}</h5> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- <div class="col-md-4 col-sm-4 col-xs-4"> --}}
                        {{-- <div class="comm-date text-left"> --}}
                        {{-- <h4>{{ $rate->created_at->format('M d Y')}}</h4> --}}
                        {{-- <div class="evaluate"> --}}
                        {{-- <input class="rating rating-loading input-id" data-min="0" data-max="5" data-step="1" value="{{$rate->rate}}" data-size="xxs" disabled> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- </li> --}}
                        {{-- @endforeach --}}
                        {{-- @if (!$ratings->count()) --}}
                        {{-- <span>لا يوجد تقييمات على هذا المنتج</span> --}}
                        {{-- @endif --}}
                        {{-- </ul> --}}
                        {{-- </div> --}}
                        {{-- <div class="write-comm" id="write-comm"> --}}
                        {{-- <h4>اكتب تعليق :</h4> --}}
                        {{-- <form> --}}
                        {{-- <div style="direction: ltr;text-align: center" class="evaluate"> --}}
                        {{-- <input class="rating rating-loading input-id" data-min="0" data-max="5" data-step="1" value="3" data-size="xs"> --}}
                        {{-- </div> --}}
                        {{-- <div class="form-group"> --}}
                        {{-- <textarea class="form-control" rows="5"></textarea> --}}
                        {{-- </div> --}}
                        {{-- <button type="submit" class="">ارسال</button> --}}
                        {{-- </form> --}}
                        {{-- </div> --}}
                        {{-- </section> --}}
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end product info + rate section -->
    <!-- start new products section -->
    <section class="new-products">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="section-title">
                        <div class="section-name">
                            <img src="/site/imgs/supplier.svg" alt="new icon">
                            منتجات مشابهة
                        </div>
                        <a href="/search" class="section-more">
                            عرض الكل
                        </a>
                    </div>
                </div>
                <div class="col-md-12 col-12">
                    <div class="first-home owl-carousel owl-theme ">
                        @foreach ($relatedProducts as $rlProduct)
                            {!! View::make('items.product')->with('product', $rlProduct)->render() !!}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end new products section -->

@endsection
