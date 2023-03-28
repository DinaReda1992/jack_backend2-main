<div class="table-responsive text-center text-center">

    <table class="table table-bordered text-center text-center">
        <thead>
            <tr>
                <th>{{ __('dashboard.order_id') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_date_creation') }}</th>
                <th style="width: 200px">{{ __('dashboard.order_time_creation') }}</th>
                <th>{{ __('dashboard.client_name') }}</th>
                @if (auth()->user()->user_type_id == 1)
                    <th>
                        {{ __('dashboard.employee_name') }}
                    </th>
                @endif
                <th>{{ __('dashboard.order_details') }}</th>
                <th> {{ __('dashboard.action_taken') }}</th>

            </tr>
        </thead>
        <tbody>

            @foreach ($objects as $object)
                <tr parent_id="{{ $object->id }}">

                    <td>{{ $object->id }}</td>
                    {{--            <td>{{ $object->updated_at->diffForHumans() }}</td> --}}
                    <td>{{ $object->created_at->format('Y-m-d') }}</td>
                    <td>{{ $object->created_at->diffForHumans() }}</td>
                    <td>
                        <a
                            href="{{ '/admin-panel/all-users/' . @$object->user->id . '/edit' }}">{{ @$object->user->username }}</a>
                    </td>
                    @if (auth()->user()->user_type_id == 1)
                        <td>
                            <a
                                href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                        </td>
                        </td>
                    @endif

                    <td>
                        <a href="{{ url('admin-panel/orders/create/' . $object->token) }}">
                            {{ __('dashboard.details') }} ( {{ @$object->cart_items->count() }} )
                        </a>

                    </td>
                    <td>
                        <a href="/admin-panel/orders/cancle_order/{{ $object->id }}">
                            <button type="button" name="button" class="btn btn-danger">
                                {{__('dashboard.delete')}}
                            </button>
                        </a>
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
        {!! $objects->render() !!}
    </div>
</div>
