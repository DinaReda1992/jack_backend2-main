@extends('website.layout')
@section('title')
    <title> {{ __('dashboard.search') }} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
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
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                            {{ __('dashboard.search') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <search-filter :categories="{{ $categories }}" settings="/uploads/{{ @\App\Models\Settings::find(39)->value }}"
            @if (auth('client')->check()) :user="{{ auth('client')->user() }}"
                @else :user="{{ @json_encode(['id' => 0, 'activate' => 0]) }}" @endif />
    </main>


@endsection
