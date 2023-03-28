@extends('layouts.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(5)->value }} - الفواتير الصادرة </title>
    <meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">
@endsection
@section('content')
    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @include('layouts.sidebar',['current'=>'outcomming-invoices'])
                </div>
                <div class="col-md-9">
                    <div class="forms">
                        <div data-active=1 id="tab-b1" class="edit-your-info a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">الفواتير الصادرة</h4>
                            <div class="my-prods">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><h3>تاريخ الفاتورة</h3></th>
                                        <th><h3>عدد المنتجات</h3></th>
                                        <th><h3>البائع</h3></th>
                                        <th><h3>حالة الفاتورة</h3></th>
                                        <th><h3>تفاصيل الفاتورة</h3></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $invoice->product_count($invoice->id) }}</td>
                                        <td>{{ $invoice->getSeller->username }}</td>
                                        <td>{{ $invoice->status==1 ? "جاري العمل على الطلب" :  "تم الانتهاء من الطلب" }}</td>
                                        <td><a data-toggle="modal" data-target="#myModal{{ $invoice->id }}" onclick="return false;" href="#">عرض تفاصيل الفاتورة</a>

                                            <div class="modal fade" id="myModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    {!! csrf_field() !!}
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">تفاصيل الفاتورة </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>
                                                                            اسم المنتج
                                                                        </th>
                                                                        <th>
                                                                            الكمية
                                                                        </th>
                                                                        <th>
                                                                            السعر
                                                                        </th>
                                                                        <th>
                                                                            السعر بعد الخصم
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @php $sum = 0 @endphp
                                                                    @foreach($invoice->getDetails as $detail)
                                                                        @php $sum = $sum+(@$detail->price_discount * @$detail->quantity) @endphp
                                                                        <tr>
                                                                            <td>
                                                                                {{ @$detail->getProduct->title }}
                                                                            </td>
                                                                            <td>
                                                                                {{ @$detail->quantity }}
                                                                            </td>
                                                                            <td>
                                                                                {{ @$detail->price * @$detail->quantity }}
                                                                            </td>
                                                                            <td>
                                                                                {{ @$detail->price_discount * @$detail->quantity }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                    <tr>
                                                                        <td colspan="3">
                                                                            الاجمالي :
                                                                        </td>
                                                                        <td>
                                                                            {{ @$sum }}
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="paginate">
                                    {{ $invoices->render() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('js')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        //get sub categories
        $('.category_id').change(function () {
            var category_id = $(this).val();
            if(category_id!=0){
                window.location.href="/my-products/"+category_id;
            }else{
                window.location.href="/my-products";
            }

        });

        $('.del').click(function () {
            var res = $(this).attr('delete_url');
            $('.res').attr('href', res);
        });
    </script>
@endsection