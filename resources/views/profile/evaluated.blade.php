@extends('layouts.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(5)->value }} - منتجات قيمتها </title>
    <meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">
@endsection
@section('content')
    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @include('layouts.sidebar',['current'=>'evaluated'])
                </div>
                <div class="col-md-9">
                    <div class="forms">
                        <div data-active=1 id="tab-b1" class="edit-your-info a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">منتجات قيمتها</h4>
                            <div class="my-prods">
                                <div class="row">
                                    @include('layouts.products')
                                </div>
                                <div class="paginate">
                                    {{ $products->render() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
                {!! csrf_field() !!}

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">رسالة الحذف </h4>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            هل أنت متأكد من الحذف ؟
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                        <a class="res" href="#">
                            <button type="button" class="btn btn-primary">نعم</button>
                        </a>
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