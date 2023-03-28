<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الرقم التعريفي</th>
{{--            <th>اسم المورد</th>--}}
{{--            <th>الرقم التعريفي للمورد</th>--}}
            <th>اسم المنتج</th>
            <th>وصف المنتج</th>
            <th>سعر الاصلي</th>
            <th>سعر المستهلك</th>
            <th>الكمية</th>
            <th>أقل كمية للطلب</th>
            <th>أقل كمية في المخزن</th>
            <th>حالة المنتج</th>
            <th>وزن الوحدة بالجرام</th>
            <th>صلاحية المنتج</th>
            <th>درجة الحرارة</th>
            <th>وحدة القياس</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($objects as $key => $object)
            <tr parent_id="{{ $object->id }}">
                <td>{{ $key + 1 }}</td>
                <td>{{ $object->id }}</td>
{{--                <td>{{ $object->user->supplier->supplier_name }}</td>--}}
{{--                <td>{{ $object->provider_id }}</td>--}}
                <td>{{ $object->title }} </td>
                <td>{{ $object->description }} </td>
                <td>{{ $object->original_price }} </td>
                <td>{{ $object->price }}</td>
                <td>{{ $object->quantity }}</td>
                <td>{{ $object->min_quantity }}</td>
                <td>{{ $object->min_warehouse_quantity }}</td>
                <td>{{ $object->stop == 1 ? 'غير مفعل' : 'مفعل' }}</td>
                <td>{{ $object->weight }}</td>
                <td>{{ $object->expiry }}</td>
                <td>{{ $object->temperature }}</td>
                <td>{{ $object->measurement->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
