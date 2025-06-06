<div class="overflow-x-autoauto">
    <table class="w-full text-sm text-left">
        <thead>
            <tr>
                <th>No.</th>
                <th>Society Name</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($society as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->society_name }}</td>
                <td>{{ $row->address_1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
nca ica 