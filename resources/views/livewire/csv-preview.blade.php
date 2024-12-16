<div>
    <h2 class="gradient-header">File Preview</h2>
    <table class="table">
        <thead>
            <tr>
                @foreach($preview['headers'] ?? [] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($preview['rows'] ?? [] as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <button wire:click="cleanFile" class="btn gradient-btn mt-3">Clean File</button>
</div>
