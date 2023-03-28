@extends('website.layout')
@section('title')
    <title>{{ $object->meta_title ?: \App\Models\Settings::find(2)->value }}
        -{{ app()->getLocale() == 'ar' ? $object->page_name : $object->page_name_en }}
    </title>
    <meta name="description" content="{{ $object->meta_description ?: \App\Models\Settings::find(3)->value }}">
    <meta name="keywords" content="{{ $object->meta_keywords ?: \App\Models\Settings::find(4)->value }}">
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
                            {{ app()->getLocale() == 'ar' ? $object->page_name : $object->page_name_en }}
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
                {!! app()->getLocale() == 'ar' ? $object->content : $object->content_en !!}
            </div>
        </section>
    </main>
@stop
