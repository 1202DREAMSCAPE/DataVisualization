<li class="list-group-item" x-data="{ editing: false }">
    <div class="flex justify-between items-center ">
        <div class="flex-1">
            <strong>{{ $label }}:</strong> {{ $value }}
        </div>
        <div>
            <button @click="editing = true" class="btn btn-sm p-2 rounded-full gradient-icon-button">
                <x-edit-icon />
            </button>
        </div>
    </div>
    <div x-show="editing" x-transition class="mt-3 ">
        <form action="{{ $route }}" method="POST" class="space-y-2">
            @csrf
            @method('PATCH')
            <div class="bg-transparent">
                <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $required ? 'required' : '' }}
                    class="form-control">
            </div>
            <div class="flex items-center space-x-3 bg-transparent">
                <button type="submit" class="gradient-button text-white rounded-sm py-2 px-3 btn font-bol btn-success btn-sm">Save</button>
                <button type="button" @click="editing = false" class="btn bg-darkgray bg-opacity-80 text-white font-bold rounded-sm py-2 px-3  btn-secondary btn-sm">Cancel</button>
            </div>
        </form>
    </div>
</li>
