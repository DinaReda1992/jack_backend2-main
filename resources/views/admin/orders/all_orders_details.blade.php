<div class="table-responsive">

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>{{ __('dashboard.order_id') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_date_creation') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_time_creation') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_from') }}</th>
                <th>{{ __('dashboard.client_name') }}</th>
                <th>{{ __('dashboard.warehouse_employee') }}</th>
                {{-- @if (\Request::segment(3) == 'suppliers-orders')
                    <th> {{ __('dashboard.supplier') }}</th>
                @endif --}}
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
            </tr>
        </thead>
        <tbody>

            @foreach ($objects as $object)
                <tr parent_id="{{ $object->id }}">

                    <td>{{ $object->id }}</td>
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
                    <td>{{ @$object->warehouse->username }}</td>
                    {{-- @if (\Request::segment(3) == 'suppliers-orders')
                        <td>
                            <a
                                href="{{ '/admin-panel/suppliers/' . @$object->provider->id . '/edit' }}">{{ @$object->provider->username }}</a>
                        </td>
                    @endif --}}

                    @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                        <td>
                            @if ($object->status == 0)
                                <a
                                    href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                        </td>
                    @elseif($object->status == 1)
                        <a
                            href="{{ '/admin-panel/all-users/' . @$object->accepted->id . '/edit' }}">{{ @$object->accepted->username }}</a>
                        </td>
                    @elseif($object->status >= 2)
                        <a
                            href="{{ '/admin-panel/all-users/' . @$object->reviewd->id . '/edit' }}">{{ @$object->reviewd->username }}</a>
                        </td>
                    @endif
                    </td>
            @endif
            @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                <td>
                    <a
                        href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                </td>

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
                <a href="{{ url('admin-panel/all-orders/' . $object->id) }}"> {{ __('dashboard.details') }} (
                    {{ @$object->cart_items->count() }} )
                </a>
            </td>
            @if ($object->status < 2)
                <td>

                </td>
            @endif
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
    </div>
</div>
