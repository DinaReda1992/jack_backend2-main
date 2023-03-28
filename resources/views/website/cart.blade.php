@extends('website.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(2)->value }} - {{__('dashboard.cart')}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value }}">
@stop
@section('content')
    <main id="content">
          <div>
           <img src="/asset/images/headerpic.jpg" alt="coverimg" width="100%" style="height:150px; object-fit:cover;"/>
      </div>
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-center">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">{{__('dashboard.cart')}}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <cart-page :items="{{ $items }}" :messages="{{ $messages }}"></cart-page>
    </main>
@endsection
