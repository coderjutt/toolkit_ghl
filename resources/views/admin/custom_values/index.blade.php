@extends('admin.layouts.index')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="{ openCreate:false, openEdit:false, openDelete:false, selected:{} }" class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-xl p-6">
            <h2 class="text-2xl font-bold text-center mb-6">📋 Custom Values</h2>

            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded-lg text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add New Button -->
            @if(isset($userPermissions['CustomValue']) && in_array('Add', $userPermissions['CustomValue']))
                <div class="text-end mb-4">

                    <button @click="openCreate = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                        + Add Custom Value
                    </button>
                </div>
            @endif
            <!-- Table -->
            <div class="overflow-x-auto">
                @if(isset($userPermissions['CustomValue']) && in_array('List', $userPermissions['CustomValue']))
                    <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="py-3 px-4 border">ID</th>
                                <th class="py-3 px-4 border">Name</th>
                                <th class="py-3 px-4 border">Value</th>
                                <th class="py-3 px-4 border">Created</th>
                                <th class="py-3 px-4 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse($values as $v)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border">{{ $v->id }}</td>
                                    <td class="py-2 px-4 border">{{ $v->name }}</td>
                                    <td class="py-2 px-4 border">{{ $v->value }}</td>
                                    <td class="py-2 px-4 border">{{ $v->created_at->format('d M Y H:i') }}</td>
                                    <td class="py-2 px-4 border flex justify-center gap-2">
                                        <!-- Edit -->
                                        @if(isset($userPermissions['CustomValue']) && in_array('Edit', $userPermissions['CustomValue']))

                                            <button
                                                @click="selected={id:{{ $v->id }}, name:'{{ $v->name }}', value:'{{ $v->value }}'}; openEdit = true;"
                                                class="px-3 py-2 bg-yellow-400 text-white rounded hover:bg-yellow-500">
                                                <i class="fa fa-pencil-alt"></i>
                                            </button>
                                        @endif
                                        <!-- Delete -->
                                        @if(isset($userPermissions['CustomValue']) && in_array('Delete', $userPermissions['CustomValue']))

                                            <button
                                                @click="selected={id:{{ $v->id }}, name:'{{ $v->name }}'}; openDelete = true;"
                                                class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-gray-500">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">
                        {{ $values->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>

            <!-- Pagination -->

        </div>

        <!-- Create Modal -->
        <div x-show="openCreate" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div @click.away="openCreate = false" class="bg-white shadow-lg rounded-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">➕ Add Custom Value</h3>
                <form method="POST" action="{{ route('admin.customvalue.store') }}">
                    @csrf
                    <input type="text" name="name" placeholder="Name"
                        class="w-full border rounded p-2 mb-3" required>
                    <input type="text" name="value" placeholder="value" class="w-full border rounded p-2 mb-3"
                        required>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 border rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="openEdit" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div @click.away="openEdit = false" class="bg-white shadow-lg rounded-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">✏️ Edit Custom Value</h3>
                <form :action="`/admin/custom-values/${selected.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" x-model="selected.name"
                        class="w-full border rounded p-2 mb-3" required>
                    <input type="text" name="value" x-model="selected.value"
                        class="w-full border rounded p-2 mb-3" required>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 border rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="openDelete" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div @click.away="openDelete = false" class="bg-white shadow-lg rounded-xl w-full max-w-sm p-6 text-center">
                <h3 class="text-lg font-bold mb-4">⚠️ Confirm Delete</h3>
                <p class="mb-4">Are you sure you want to delete <b x-text="selected.name"></b>?</p>
                <form :action="`/admin/custom-values/${selected.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center gap-2">
                        <button type="button" @click="openDelete = false" class="px-4 py-2 border rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection