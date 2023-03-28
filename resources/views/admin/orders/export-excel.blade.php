<table>
    <thead>
        <tr>
            {{-- <th>{{ __('dashboard.supplier_id') }}</th> --}}
            {{-- <th>{{ __('dashboard.supplier_name') }} </th> --}}
            <th>{{ __('dashboard.product_id') }}</th>
            <th>{{ __('dashboard.product_name') }} </th>
            <th>{{ __('dashboard.quantity_in_orders') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($carts as $cart)
            <tr>
                {{-- <td>{{ $cart->supplier_id }}</td>
                <td>{{ $cart->supplier_name }}</td> --}}
                <td>{{ $cart->id }}</td>
                <td>{{ $cart->title }}</td>
                <td>{{ $cart->new_quantity }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
