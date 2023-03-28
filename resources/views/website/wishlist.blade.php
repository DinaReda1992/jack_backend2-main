@extends('website.layout')
@section('title')
    <title> {{__('dashboard.wishlist')}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@stop
@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">{{__('dashboard.wishlist')}}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="pb-11 pb-lg-13">
            <div class="container">
                @if (Session::has('message'))
                    <div class="alert alert-info" role="alert" style="text-align: center;">
                        {{ Session::get('message') }}
                    </div>
                @endif
                <h2 class="text-center mt-9 mb-8">{{__('dashboard.wishlist')}}</h2>
                <div class="row">
                    @foreach ($favoriteProducts as $fProduct)
                        <div class="favpage col-md-3" dir="ltr">
                            @php
                                $fProduct = json_decode(json_encode(App\Http\Resources\ProductResources::make($fProduct)));
                            @endphp
                            {!! View::make('items.product')->with('product', $fProduct)->render() !!}
                        </div>
                    @endforeach
                    @if ($favoriteProducts->count() == 0)
                        <div class="col-md-12 col-12 text-center" dir="ltr">
                            <img src="/images/cartfav.png" style="height: 300px;"  alt="cart" />
                            <h3>{{__('dashboard.not_found_data_in_wishlist')}}</h3>
                        </div>
                    @endif
                </div>
                <div class="col-md-12 text-center mt-2 mt-md-5">
                    <nav aria-label="Page navigation example">
                        {!! $favoriteProducts->render() !!}
                    </nav>
                </div>
            </div>
        </section>
    </main>

@endsection
