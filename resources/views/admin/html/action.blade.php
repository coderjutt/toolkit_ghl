<div class="flex space-x-2">
    {{-- Edit Action --}}
    @if(isset($actions['edit']) && $actions['edit'])
        <a href="{{ route($route . '.edit', $id) }}" class="px-3 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
            Edit
        </a>
    @endif

    {{-- Delete Action --}}
    @if(isset($actions['delete']) && $actions['delete'])
        <button type="button" class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700 confirm-delete" data-id="{{ $id }}">
            Delete
        </button>
    @endif
</div>

