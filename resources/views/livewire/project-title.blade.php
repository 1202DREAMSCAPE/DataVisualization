<div>
    <h2>{{ $project->title }}</h2>
    <button wire:click="$emit('renameTitle')">Rename</button>

    <!-- Rename Modal -->
    <div x-data="{ showModal: false }">
        <button @click="showModal = true">Rename Title</button>
        <div x-show="showModal">
            <input type="text" wire:model.lazy="project.title">
            <button @click="showModal = false; $wire.updateTitle(project.title)">Save</button>
        </div>
    </div>
</div>
