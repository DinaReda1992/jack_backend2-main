<div class="table-responsive">
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th style="width: 200px">تاريخ الطلب</th>
                <th>المورد</th>
                <th>اسم السائق</th>
                <th>شروط الدفع</th>
                <th>حالة الطلب</th>
                <th>تفاصيل الطلب</th>
                <th>الاجراء المتخذ</th>
            </tr>
        </thead>
        <tbody>

            @forelse ($objects as $object)
                <tr parent_id="{{ $object->id }}">

                    <td>{{ $object->id }}</td>
                    <td>{{ @$object->created_at->translatedFormat('Y-m-d h:i a') }}</td>
                    <td>{{ @$object->provider->username }}</td>
                    <td>{{ @$object->driver->username }}</td>
                    <td>{{ @$object->paymentTerm->name }}</td>
                    <td>
                        <a href="/admin-panel/purchases-orders/{{ $object->id }}"
                            style="color:{{ $object->status == 5 ? 'red' : '' }}">{{ @$object->orderStatus->name }}</a>
                    </td>

                    <td>
                        <a href="/admin-panel/purchases-orders/{{ $object->id }}">
                            تفاصيل الطلب ({{ @$object->purchase_item->count() }}
                            منتجات)
                        </a>


                    </td>

                    <td style="text-align: center">
                        @if ($object->status == 5)
                            <span style="color:red">تم الغاء الطلب</span>
                        @else
                            @if ($object->status == 7)
                                <span style="color:green">مكتمل </span>
                            @elseif($object->status < 3)
                                <span class="badge badge-info">{{ $object->orderStatus->name }} </span>
                            @elseif($object->status >= 3)
                                <a onclick="return false;" data-toggle="modal"
                                    data-target="#myModal{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-primary">
                                        {{ $object->orderStatus->btn_text }}</button>
                                </a>
                                <div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">رسالة تغيير حالة الطلب</h4>
                                            </div>
                                            <form method="post"
                                                action="/admin-panel/purchases-orders/change_order_status/{{ $object->id }}">
                                                <div class="modal-body">
                                                    @csrf
                                                    @if ($object->status == 6)
                                                        <div class="table">
                                                            <table class="table table-bordered">
                                                                <tr style="background: #e8e8e8">
                                                                    <th>المنتج</th>
                                                                    <th>الكمية المطلوبة</th>
                                                                    <th>الكمية المستلمة</th>

                                                                </tr>
                                                                @foreach ($object->purchase_item as $key => $item)
                                                                    <tr>
                                                                        <td>{{ @$item->product->title }}</td>
                                                                        <td>{{ @$item->quantity }} </td>
                                                                        <td><input value="{{ $item->quantity }}"
                                                                                name="items[{{ $key }}][quantity]" />
                                                                            <input type="hidden"
                                                                                value="{{ $item->id }}"
                                                                                name="items[{{ $key }}][id]" />
                                                                            <input type="hidden"
                                                                                value="{{ $object->id }}"
                                                                                name="id" />

                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    @endif
                                                    <div class="form-group mt-2">
                                                        <label for="exampleInputEmail1"></label>
                                                        هل انت متأكد من تغيير حالة هذا الطلب الى
                                                        ({{ $object->orderStatus->btn_text }})
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">اغلاق</a>
                                                    <button type="submit" class="btn btn-primary">ارسال</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endif


                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="8">لايوجد طلبات</td>
            </tr>
            @endforelse    
        </tbody>
    </table>

</div>
<div class="clearfix"></div>
<br>
<div class="datatable-footer" style="    padding: 10px 30px 0px 30px;">
    @php
        $from_page = $objects->count() * $objects->currentPage() - $objects->count() + 1;
        $from_page = $objects->currentPage() == $objects->lastPage() ? $objects->lastItem() - $objects->Count() + 1 : $from_page;
        
        $to_page = $objects->currentPage() * $objects->perPage();
        $to_page = $to_page > $objects->total() ? $objects->total() : $to_page;
    @endphp
    <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">يظهر
        {{ $from_page }} الى
        {{ $to_page }} من {{ $objects->total() }} اجمالي
        الصفوف
    </div>
    <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
        {{ $objects->appends(Request::except('page'))->links() }}
    </div>
</div>
