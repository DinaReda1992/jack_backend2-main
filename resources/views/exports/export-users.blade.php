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
        <th>الرقم الضريبي</th>
    </tr>
    </thead>
    <tbody>
    @php $i=1 @endphp
    @foreach($objects as $object)
        <tr parent_id="{{ $object->id }}">
            <td>{{ $i }}</td>
            <td>{{ $object->id }}</td>
            <td>{{ $object->username }} </td>
            <td>{{ $object->created_at }} </td>
            <td style="direction: ltr">+({{ $object->phonecode }}){{ $object->phone }}</td>
            <td >{{ $object->email }}</td>
            <td align="center" class="center">
                @if($object->user_type_id==5)
                    {{@$object->addresses()->where('is_home',1)->first()->region->name}}
                @endif
            </td>
            <td>{{ $object->tax_number }}</td>
        </tr>
        @php $i++ @endphp
    @endforeach

    </tbody>
</table>
