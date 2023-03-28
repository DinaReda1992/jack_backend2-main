<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الرقم التعريفي</th>
            <th>اسم المستخدم</th>
            <th>تاريخ الإنضمام</th>
            <th>رقم الجوال</th>
            <th>البريد الاكتروني</th>
            <th>المنطقة</th>
            <th>المدينة</th>
            <th>عدد المنتجات</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @php $i=1 @endphp
        @foreach ($objects as $object)
            <tr parent_id="{{ $object->id }}">
                <td>{{ $i }}</td>
                <td>{{ $object->id }}</td>
                <td>{{ $object->username }} </td>
                <td>{{ $object->created_at }} </td>
                <td style="direction: ltr">+({{ $object->phonecode }}){{ $object->phone }}</td>
                <td>{{ $object->email }}</td>
                <td>{{ @$object->region->name }}</td>
                <td>{{ @$object->state->name }}</td>
                <td>{{ @$object->products->count() }} </td>
                <td>{{ @$object->supplier->stop == 1 ? 'معطل' : 'مفعل' }}</td>

            </tr>
            @php $i++ @endphp
        @endforeach

    </tbody>
</table>
