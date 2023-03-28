@extends('website.layout')
@section('title')
    <title>{{__('dashboard.wallet')}}</title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@endsection

@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">{{__('dashboard.wallet')}}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="pb-11 pb-lg-13">
            <div class="container">
                <h2 class="text-center mt-9 mb-8">{{__('dashboard.wallet')}}</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="wallet-container text-center">
                            <div class="amount-box text-center">
                                <img src="https://lh3.googleusercontent.com/ohLHGNvMvQjOcmRpL4rjS3YQlcpO0D_80jJpJ-QA7-fQln9p3n7BAnqu3mxQ6kI4Sw"
                                    alt="wallet" style="height: 100px">
                                <p>{{__('dashboard.wallet')}}</p>
                                <p class="amount">{{ round($balance, 2) }} {{__('dashboard.sar')}}</p>
                            </div>

                            <div class="txn-history">
                                @forelse($objects as $object)
                                    <p class="txn-list p-4 "
                                        style="border-radius: 5px;color:white; {{ $object->price >= 0 ? 'background:green;' : 'background:red;' }}">
                                        <span style="float:right;"> {{ $object->notes }}</span>
                                        <span>{{ $object->price }}{{__('dashboard.sar')}}</span>
                                        <span style="float: left">{{ $object->created_at->diffforhumans() }}</span>
                                    </p> 
                                    <div class="clearfix"></div>
                                @empty
                                    <div>
                                        <p class="alert alert-info px-2 py-2">{{__('trans.No transactions')}}</p>
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
