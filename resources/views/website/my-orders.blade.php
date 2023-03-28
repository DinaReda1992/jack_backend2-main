@extends('website.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(2)->value }} - {{ __('dashboard.my_orders') }} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value }}">
@stop
@section('content')
    <main id="content">
        <div>
            <img src="/asset/images/headerpic.jpg" alt="coverimg" width="100%" style="height:150px; object-fit:cover;" />
        </div>
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body"
                                href="/">{{ __('dashboard.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                            {{ __('dashboard.my_orders') }}
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
                <h2 class="text-center mt-9 mb-8">{{ __('dashboard.my_orders') }}</h2>
                <form class="table-responsive-md pb-8 pb-lg-10">
                    <table class="table border order">
                        <thead style="background-color: #F5F5F5">
                            <tr class="fs-15 letter-spacing-01 font-weight-600 text-uppercase text-secondary">
                                <th scope="col" class="border-1x">{{ __('dashboard.id') }}</th>
                                <th scope="col" class="border-1x">{{ __('dashboard.order_status') }}</th>
                                <th scope="col" class="border-1x">{{ __('dashboard.order_date') }}</th>
                                <th scope="col" class="border-1x">{{ __('dashboard.invoice') }}</th>
                                <th scope="col" class="border-1x">{{ __('dashboard.action_taken') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $order)
                                <tr class="position-relative">
                                    <th scope="row" class="">
                                        <div class="media align-items-center">
                                            <div class="media-body ">
                                                <p class="font-weight-500 mb-1 text-secondary">{{ $order->id }}</p>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="align-middle">
                                        <div class="input-group position-relative">
                                            <p class="font-weight-300 mb-1 text-secondary">
                                                {{ @$order->orderStatus->name ? (app()->getLocale() == 'en' ? $order->orderStatus->name_en : $order->orderStatus->name) : 'جاري المراجعة' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <p class="mb-0 text-secondary">{{ $order->created_at->format('Y-m-d') }}</p>
                                    </td>

                                    <td class="align-middle">
                                        <p class="mb-0">
                                            <a href="{{ url('/i/' . $order->short_code) }}"
                                                class="text-success">{{ __('dashboard.view_invoice') }}</a>
                                        </p>
                                    </td>
                                    <td class="align-middle">
                                        <p class="mb-0 text-secondary font-weight-bold">
                                            @if ($order->status == 0)
                                                @if ($order->payment_method != 5)
                                                    <a class="btn btn-danger d-inline-block"
                                                        href="{{ url('/my-orders/cancle_client_order', $order->id) }}">{{ __('dashboard.cancel_order') }}</a>
                                                @endif
                                            @endif
                                            @if ($order->payment_method == 5 && $order->transfer_photo == null && $order->status != 5)
                                                {{-- <a class="d-inline-block" style="width: 119px" onclick="return false;"
                                                    data-toggle="modal" data-target="#cancelModal{{ $order->id }}555"
                                                    href="#">
                                                    اضافة ايصال الدفع
                                                </a>
                                                <div class="modal fade" id="cancelModal{{ $order->id }}555"
                                                    tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h6 class="modal-title" id="myModalLabel">اضافة ايصال
                                                                    الدفع
                                                                    طلب رقم {{ $order->id }}</h6>
                                                            </div>

                                                            <div class="modal-body">
                                                                <form
                                                                    action="/my-orders/upload-invoice/{{ $order->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input required class="form-control" name="photo"
                                                                        type="file" />
                                                                    <button type="submit"
                                                                        class="btn btn-primary mt-2">{{ __('dashboard.save') }}</button>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a type="button" class="btn btn-default"
                                                                    data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            @elseif(@$order->transfer_photo->photo != '' && $order->status != 5)
                                                <span class="badge badge-info p-1 d-inline-block">
                                                    {{ __('dashboard.Payment receipt has been added') }}</span>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"> {{ __('dashboard.not_found_orders') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
                <div class="row">
                    <div class="col-12">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
