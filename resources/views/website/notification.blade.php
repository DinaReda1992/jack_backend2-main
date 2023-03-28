@extends('website.layout')
@section('title')
    <title> {{ __('dashboard.notifications') }}</title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@endsection

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
                            {{ __('dashboard.notifications') }}
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
                <h2 class="text-center mt-9 mb-8">{{ __('dashboard.notifications') }}</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="wallet-container text-center">
                            <div class="txn-history">
                                @forelse($objects as $object)
                                    <p class="txn-list">
                                        <span>{{ App::getLocale() == 'en' ? $object->message_en : $object->message }}</span>
                                        <span class="credit-amount">
                                            {{ $object->created_at->diffforhumans() }}</span>
                                    </p>
                                    <div class="clearfix"></div>
                                @empty
                                    <div>
                                        <p>{{__('dashboard.not_found_notifications')}}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
