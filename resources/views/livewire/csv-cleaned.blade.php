<div>
    <h2 class="gradient-header">Cleaned File</h2>
    <table class="table">
        <thead>
            <tr>
                @foreach($cleanedPreview['headers'] ?? [] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($cleanedPreview['rows'] ?? [] as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <ul>
        <li>Total Rows Removed: {{ $summary['total_rows_removed'] }}</li>
        <li>Remaining Rows: {{ $summary['remaining_rows'] }}</li>
    </ul>
</div>
