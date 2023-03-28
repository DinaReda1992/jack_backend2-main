<table>
    <thead>
        <tr>
            <th>{{__('dashboard.orders_status')}}</th>
            <th>{{__('dashboard.count_orders')}} </th>
            <th>{{__('dashboard.sales')}} </th>
            <th>{{__('dashboard.percentage_of_orders_from_status')}}</th>
            <th>{{__('dashboard.percentage_of_orders_from_all')}}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>{{__('dashboard.new')}}</th>
            <td>{{ $data['new_orders'] }}</td>
            <td>{{ round($data['new_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_new_orders'] != 0 ? round(($data['new_orders'] / $data['all_new_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_new_orders'] != 0 ? round(($data['new_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>

        </tr>
        <tr>
            <th>{{__('dashboard.waiting_for_confirmation')}}</th>
            <td>{{ $data['pending_orders'] }}</td>
            <td>{{ round($data['pending_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_pending_orders'] != 0 ? round(($data['pending_orders'] / $data['all_pending_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_pending_orders'] != 0 ? round(($data['pending_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
        <tr>
            <th>{{__('dashboard.preparing')}}</th>
            <td>{{ $data['preparing_orders'] }}</td>
            <td>{{ round($data['preparing_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_preparing_orders'] != 0 ? round(($data['preparing_orders'] / $data['all_preparing_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_preparing_orders'] != 0 ? round(($data['preparing_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
        <tr>
            <th>{{__('dashboard.ready_to_ship')}}</th>
            <td>{{ $data['ready_to_ship_orders'] }}</td>
            <td>{{ round($data['ready_to_ship_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_ready_to_ship_orders'] != 0 ? round(($data['ready_to_ship_orders'] / $data['all_ready_to_ship_orders']) * 100, 2) : 0 }}
                %
            </td>
            <td>{{ $data['all_ready_to_ship_orders'] != 0 ? round(($data['ready_to_ship_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
        <tr>
            <th>{{__('dashboard.shipped')}}</th>
            <td>{{ $data['shipped_orders'] }}</td>
            <td>{{ round($data['shipped_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_shipped_orders'] != 0 ? round(($data['shipped_orders'] / $data['all_shipped_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_shipped_orders'] != 0 ? round(($data['shipped_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
        <tr>
            <th>{{__('dashboard.delivering')}}</th>
            <td>{{ $data['delivering_orders'] }}</td>
            <td>{{ round($data['delivering_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_delivering_orders'] != 0 ? round(($data['delivering_orders'] / $data['all_delivering_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_delivering_orders'] != 0 ? round(($data['delivering_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
        <tr>
            <th>{{__('dashboard.completed')}}</th>
            <td>{{ $data['completed_orders'] }}</td>
            <td>{{ round($data['completed_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_completed_orders'] ? round(($data['completed_orders'] / $data['all_completed_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_completed_orders'] ? round(($data['completed_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
        <tr>
            <th>{{__('dashboard.cancelled')}}</th>
            <td>{{ $data['canceled_orders'] }}</td>
            <td>{{ round($data['canceled_orders_price'], 2) }} {{__('dashboard.sar')}}</td>
            <td>{{ $data['all_canceled_orders'] ? round(($data['canceled_orders'] / $data['all_canceled_orders']) * 100, 2) : 0 }}
                %</td>
            <td>{{ $data['all_canceled_orders'] ? round(($data['canceled_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                %</td>
        </tr>
    </tbody>
</table>
