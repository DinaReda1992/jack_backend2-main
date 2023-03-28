<div class="table-responsive text-center text-center">

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>{{ __('dashboard.order_id') }}</th>
                <th>{{ __('dashboard.main_order_id') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_date_creation') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_time_creation') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_from') }}</th>
                <th>{{ __('dashboard.client_name') }}</th>
                @if (\Request::segment(3) == 'suppliers-orders')
                    <th> {{ __('dashboard.supplier') }}</th>
                @endif
                @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                    <th>
                        {{ __('dashboard.employee_name') }}
                    </th>
                    <th>
                        {{ __('dashboard.marketers_name') }}
                    </th>
                @endif
                <th>
                    {{ __('dashboard.region') }}
                </th>
                @if (\Request::segment(2) == 'orders')
                    <th>
                        {{ __('dashboard.receipt_attached') }}
                    </th>
                @endif
                <th style="width: 127px">{{ __('dashboard.order_details') }}</th>

                <th> {{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($objects as $object)
                <tr parent_id="{{ $object->id }}">
                    <td>{{ $object->id }}</td>
                    <td><a href="/admin-panel/new-orders/{{ $object->parent_order }}">{{ $object->parent_order }}</a>
                    </td>
                    @if ($object->status == 0)
                        <td>{{ $object->created_at->format('Y-m-d') }}</td>
                        <td>{{ $object->created_at->diffForHumans() }}</td>
                    @elseif($object->status == 1)
                        <td>{{ $object->created_at->format('Y-m-d') }}</td>
                        <td>{{ $object->created_at->diffForHumans() }}</td>
                    @elseif($object->status >= 2)
                        <td>{{ $object->created_at->format('Y-m-d') }}</td>
                        <td>{{ $object->created_at->diffForHumans() }}</td>
                    @endif
                    <td>
                        @if (!$object->platform)
                            App
                        @else
                            {{ @$object->platform }}
                        @endif
                    </td>
                    <td>
                        <a
                            href="{{ '/admin-panel/all-users/' . @$object->user->id . '/edit' }}">{{ @$object->user->username }}</a>
                    </td>
                    @if (\Request::segment(3) == 'suppliers-orders')
                        <td>
                            <a
                                href="{{ '/admin-panel/suppliers/' . @$object->provider->id . '/edit' }}">{{ @$object->provider->username }}</a>
                        </td>
                    @endif
                    @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                        <td>
                            @if ($object->status == 0)
                                <a
                                    href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                            @elseif($object->status == 1)
                                <a
                                    href="{{ '/admin-panel/all-users/' . @$object->accepted->id . '/edit' }}">{{ @$object->accepted->username }}</a>
                            @elseif($object->status >= 2)
                                <a
                                    href="{{ '/admin-panel/all-users/' . @$object->reviewd->id . '/edit' }}">{{ @$object->reviewd->username }}</a>
                            @endif
                        </td>
                    @endif
                    @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                        <td>
                            <a
                                href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                        </td>
                    @endif
                    <td>
                        {{ @$object->region->name }}
                    </td>
                    @if (\Request::segment(2) == 'orders')
                        <td>
                            @if (@$object->payment_method == 3)
                                <span class="badge badge-success">{{ __('dashboard.pay_with_the_balance') }}</span>
                            @elseif($object->payment_method == 2)
                                <span class="badge badge-success">{{ __('dashboard.electronic_payment') }}</span>
                            @else
                                @if (@$object->transfer_photo->photo != '')
                                    <span class="badge badge-success">{{ __('dashboard.receipt_attached') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('dashboard.unpaid') }}</span>
                                @endif
                            @endif
                        </td>
                    @endif
                    <td>
                        @if ($object->status == 0)
                            <a href="{{ url('admin-panel/orders/' . $object->id) }}"> {{ __('dashboard.details') }} (
                                {{ @$object->cart_items->count() }} )
                            </a>
                        @elseif($object->status == 1)
                            <a href="{{ url('admin-panel/new-orders/' . $object->id) }}">
                                {{ __('dashboard.details') }} (
                                {{ @$object->cart_items->count() }} )
                            </a>
                        @elseif($object->status >= 2)
                            <a href="{{ url('admin-panel/warehouse/' . $object->id) }}">
                                {{ __('dashboard.details') }} (
                                {{ @$object->cart_items->count() }} )
                            </a>
                        @endif
                    </td>
                    @if ($object->status < 2)
                        <td>
                            @if ($object->status == 0)
                                <a onclick="return false;" data-toggle="modal"
                                    data-target="#approve_order{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-primary">
                                        {{ __('dashboard.confirm_order') }}
                                    </button>
                                </a>
                                <div class="modal fade" id="approve_order{{ $object->id }}555" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    {{ __('dashboard.confirm_order') }}
                                                </h4>
                                            </div>
                                            <form method="get"
                                                action="/admin-panel/approve_order/{{ $object->id }}">
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        {{ __('dashboard.are_you_sure_to_accept_the_order') }}
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <a onclick="return false;" data-toggle="modal"
                                    data-target="#cancelModal{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-danger">
                                        {{ __('dashboard.cancel') }}</button>
                                </a>
                                <div class="modal fade" id="cancelModal{{ $object->id }}555" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    {{ __('dashboard.message_cancel_order') }}</h4>
                                            </div>
                                            <form method="get"
                                                action="/admin-panel/orders/cancle_client_order/{{ $object->id }}">
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        {{ __('dashboard.are_you_sure_you_want_to_cancel_this_order') }}
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif($object->status == 1)
                                <a onclick="return false;" data-toggle="modal"
                                    data-target="#1approve_order{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-primary">
                                        {{ __('dashboard.confirm_order') }}
                                    </button>
                                </a>
                                <div class="modal fade" id="1approve_order{{ $object->id }}555" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    {{ __('dashboard.confirm_order') }}</h4>
                                            </div>
                                            <form method="get"
                                                action="/admin-panel/new-orders/approve_order/{{ $object->id }}">
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        {{ __('dashboard.are_you_sure_to_accept_the_order') }}
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <a onclick="return false;" data-toggle="modal"
                                    data-target="#2cancelModal{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-danger">
                                        {{ __('dashboard.cancel') }}</button>
                                </a>
                                <div class="modal fade" id="2cancelModal{{ $object->id }}555" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    {{ __('dashboard.message_cancel_order') }}</h4>
                                            </div>
                                            <form method="get"
                                                action="/admin-panel/new-orders/cancle_order/{{ $object->id }}">
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        {{ __('dashboard.are_you_sure_you_want_to_cancel_this_order') }}
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    @endif

                    <td>
                        @if (in_array($object->status, [2]))
                            @if ($object->stop == 0 && $object->return_to_wallet == 0)
                                <a onclick="return false;" data-toggle="modal"
                                    data-target="#4approve_order{{ $object->id }}555" href="#">
                                    <button type="button" name="button" class="btn btn-primary">
                                        {{ @$object->orderStatus->btn_text }}
                                    </button>
                                </a>
                                <div class="modal fade" id="4approve_order{{ $object->id }}555" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">
                                                    {{ __('dashboard.message_change_status_order') }}</h4>
                                            </div>
                                            <form method="get"
                                                action="/admin-panel/warehouse/approve_order/{{ $object->id }}">
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"></label>
                                                        {{ __('dashboard.are_you_sure_from') }}
                                                        {{ @$object->orderStatus->btn_text }} ؟
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a type="button" class="btn btn-default"
                                                        data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($object->stop == 1)
                                <button type="button" name="button" class="btn btn-warning">
                                    {{ __('dashboard.order_pending') }}
                                </button>
                            @elseif ($object->return_to_wallet == 1)
                                <button type="button" name="button" class="btn btn-warning">
                                    {{ __('dashboard.the_order_amount_has_been_returned_to_the_wallet') }} </button>
                            @endif
                        @endif

                    </td>
                </tr>
            @endforeach
            @if (count($objects) == 0)
                <tr>
                    <td colspan="11" align="center">{{ __('dashboard.not_found_orders') }}</td>
                </tr>
            @endif

        </tbody>
    </table>

    <div class="clearfix"></div>
    <br>
    <hr>
    <div align="center">
        {{ $objects->appends(Request::except('page'))->links() }}

        {{-- {!! $objects->render() !!} --}}
    </div>
</div>
