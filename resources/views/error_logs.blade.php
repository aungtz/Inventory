<div style="padding: 20px; font-family: sans-serif;">
    <h2>System Error Logs</h2>
    <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
        <thead style="background: #f44336; color: white;">
            <tr>
                <th>ID</th>
                <th>Path/Form</th>
                <th>Error Message</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->ID }}</td>
                <td>{{ $log->FormName }}</td>
                <td style="color: red;">{{ $log->ErrorMessage }}</td>
                <td>{{ $log->UserName }}</td>
                <td>{{ $log->InsertedDate }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>