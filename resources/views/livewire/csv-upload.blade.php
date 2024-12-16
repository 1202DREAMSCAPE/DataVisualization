<div>
    <h2 class="gradient-header">Upload CSV or XLSX File</h2>
    <form wire:submit.prevent="upload">
        <input type="file" wire:model="file" class="form-control" accept=".csv,.xlsx,.xls">
        @error('file') <span class="text-danger">{{ $message }}</span> @enderror
        <button type="submit" class="btn gradient-btn mt-3">Upload</button>
    </form>
</div>
