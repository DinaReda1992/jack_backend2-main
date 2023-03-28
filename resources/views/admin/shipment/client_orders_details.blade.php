 <div class="table-responsive">

    <table class="table table-bordered text-center text-center">
        <thead>
            <tr>
                <th>{{__('dashboard.order_id')}}</th>
                <th style="width: 200px">{{__('dashboard.order_date_creation')}}</th>
                <th style="width: 200px">{{__('dashboard.order_time_creation')}}</th>
                <th style="width: 200px">{{__('dashboard.order_from')}}</th>
                <th>{{__('dashboard.client_name')}}</th>
                <th>{{__('dashboard.receipt_code')}}</th>
                <th>{{__('dashboard.region')}}</th>
                <th style="width: 127px">{{__('dashboard.order_details')}}</th>
                <th> {{__('dashboard.action_taken')}}</th>
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
                    <td>{{ $object->code }}</td>
                    <td>
                        {{ @$object->region->name }}
                    </td>
                    <td>
                        @if ($object->status == 3)
                            <a href="{{ url('admin-panel/client-orders/' . $object->id) }}"> {{__('dashboard.details')}} (
                                {{ @$object->cart_items->count() }} )
                            </a>
                        @elseif($object->status > 3)
                            <a href="{{ url('admin-panel/warehouse/' . $object->id) }}"> {{__('dashboard.details')}} (
                                {{ @$object->cart_items->count() }} )
                            </a>
                        @endif
                    </td>
                    <td>
                        @if ($object->status == 3)
                            <a href="/admin-panel/client-orders/{{ $object->id }}" class="btn btn-primary">
                                @if (!$object->driver_id)
                                   {{__('dashboard.select_driver')}}
                                @else
                                   {{__('dashboard.change_driver')}}
                                @endif
                            </a>
                        @elseif(in_array($object->status, [4, 6]))
                            <form style="display: inline-block"
                                action="/admin-panel/warehouse/approve_order/{{ $object->id }}" method="GET">
                                @csrf
                                <button type="submit"
                                    class="btn btn-primary submit_form_btn">{{ @$object->orderStatus->btn_text }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if (count($objects) == 0)
                <tr>
                    <td colspan="11" align="center">{{__('dashboard.not_found_orders')}}</td>
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
