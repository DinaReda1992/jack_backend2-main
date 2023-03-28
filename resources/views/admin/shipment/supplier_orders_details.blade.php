 <div class="table-responsive text-center text-center">
    <table class="table table-bordered text-center text-center">
        <thead>
            <tr>
                <th>{{__('dashboard.order_id')}}</th>
                <th style="width: 200px">{{__('dashboard.order_date')}}</th>
                <th>{{__('dashboard.supplier')}}</th>
                <th>{{__('dashboard.payment_terms')}}</th>
                <th>{{__('dashboard.order_status')}}</th>
                <th>{{__('dashboard.order_details')}}</th>
                <th>{{__('dashboard.action_taken')}}</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($objects as $object)
                <tr parent_id="{{ $object->id }}">

                    <td>{{ $object->id }}</td>
                    <td>{{ @$object->created_at->diffForHumans() }}</td>
                    <td>{{ @$object->provider->username }}</td>

                    <td>
                        {{ @$object->paymentTerm->name }}
                    </td>

                    <td>
                        <a href="/admin-panel/supplier-orders/{{ $object->id }}"
                            style="color:{{ $object->status == 5 ? 'red' : '' }}">{{ @$object->orderStatus->name }}</a>
                    </td>

                    <td>
                        <a href="/admin-panel/supplier-orders/{{ $object->id }}">
                            {{__('dashboard.order_details')}} ({{ @$object->purchase_item->count() }}
                            {{__('dashboard.products')}})
                        </a>


                    </td>
                    <td>
                        @if ($object->status == 7 || $object->status == 8)
                            <span style="color:green">{{__('dashboard.delivered')}} </span>
                        @elseif ($object->status == 5)
                            <span style="color:red">{{__('dashboard.cancelled')}} </span>
                        @elseif(in_array($object->status, [3, 4, 6]))
                            @if ($object->status == 3)
                                <a href="/admin-panel/supplier-orders/{{ $object->id }}" class="btn btn-primary">
                                    @if (!$object->driver_id)
                                       {{__('dashboard.select_driver')}}
                                    @else
                                       {{__('dashboard.change_driver')}}
                                    @endif
                                </a>
                            @endif
                            @if (($object->status == 3 && $object->driver_id) || $object->status == 4 || $object->status == 6)
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
                                                <h4 class="modal-title" id="myModalLabel">{{__('dashboard.message_change_status_order')}}</h4>
                                            </div>
                                            <form method="post"
                                                action="/admin-panel/purchases-orders/change_order_status/{{ $object->id }}">
                                                {{ csrf_field() }}
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        {{__('dashboard.are_you_sure_you_want_to_change_the_status_of_this_order')}}
                                                        ({{ $object->orderStatus->btn_text }})
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{__('dashboard.close')}}</a>
                                                    <button type="submit" class="btn btn-primary">{{__('dashboard.send')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach


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
