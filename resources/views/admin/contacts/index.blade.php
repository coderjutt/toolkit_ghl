@extends('admin.layouts.index')

@section('content')
    <div x-data="{ openCreate:false, openEdit:null, openDelete:null }" class="p-6">

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-center">📇 Contacts</h2>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Button -->
            @if(isset($userPermissions['contacts']) && in_array('Add', $userPermissions['contacts']))
                <div class="text-end mb-4">
                    <button @click="openCreate = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        + Add Contact Button 
                    </button>
                </div>
            @endif
            <!-- Table -->
            <div class="overflow-x-auto">
                @if(isset($userPermissions['contacts']) && in_array('List', $userPermissions['contacts']))
                        <table class="w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">ID</th>
                                    <th class="px-4 py-2 border">Title</th>
                                    <th class="px-4 py-2 border">Action</th>
                                    <th class="px-4 py-2 border">URL</th>
                                    <th class="px-4 py-2 border">Folder</th>
                                    <th class="px-4 py-2 border">Color</th>
                                    <th class="px-4 py-2 border">Background</th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @forelse($contacts as $c)
                                    <tr>
                                        <td class="border px-2 py-1">{{$c->id}}</td>
                                        <td class="border px-2 py-1">{{ $c->title }}</td>
                                        <!-- <td class="border px-2 py-1">{{ $c->action }}</td> -->
                                        <td class="border px-2 py-1">
                                            @php
                                                $actions = json_decode($c->action, true);
                                            @endphp
                                            @if(is_array($actions))
                                                {{ collect($actions)->pluck('value')->implode(', ') }}
                                            @else
                                                {{ $c->action }}
                                            @endif
                                        </td>
                                        <td class="border px-2 py-1">{{ $c->url }}</td>
                                        <td class="border px-2 py-1">{{ $c->folder ?? 'No Folder' }}</td>
                                        <td class="border px-2 py-1">
                                            <div class="w-6 h-6 mx-auto rounded" style="background: {{ $c->color }}"></div>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <div class="w-6 h-6 mx-auto rounded" style="background: {{ $c->background }}"></div>
                                        </td>
                                        <td class="border px-2 py-1 flex justify-center gap-2">
                                            @if(isset($userPermissions['contacts']) && in_array('Edit', $userPermissions['contacts']))
                                                <button @click="openEdit={{ $c->id }}"
                                                    class="px-3 py-1 bg-yellow-400 text-white rounded">✏️</button>
                                            @endif
                                            @if(isset($userPermissions['contacts']) && in_array('Delete', $userPermissions['contacts']))
                                                <button @click="openDelete={{ $c->id }}"
                                                    class="px-3 py-1 bg-red-500 text-white rounded">🗑️</button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->

                                    <div x-show="openEdit==={{ $c->id }}" x-cloak
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                        <div class="bg-white p-6 rounded-lg w-full max-w-lg" @click.away="openEdit=null">
                                            <h3 class="text-lg font-bold mb-4">✏️ Edit Contact</h3>
                                            <form method="POST" action="{{ route('admin.contacts.update', $c->id) }}">
                                                @csrf @method('PUT')
                                                @include('admin.contacts.partials.form', ['contact' => $c])
                                            </form>
                                        </div>
                                    </div>


                                    <!-- Delete Modal -->
                                    <div x-show="openDelete==={{ $c->id }}" x-cloak
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                        <div class="bg-white p-6 rounded-lg w-full max-w-md text-center" @click.away="openDelete=null">
                                            <h3 class="font-bold mb-2">⚠️ Confirm Delete</h3>
                                            <p>Delete <b>{{ $c->title }}</b>?</p>
                                            <form method="POST" action="{{ route('admin.contacts.destroy', $c->id) }}"
                                                class="mt-4 flex justify-center gap-2">
                                                @csrf @method('DELETE')
                                                <button type="button" @click="openDelete=null"
                                                    class="px-4 py-2 border rounded">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                                            </form>
                                        </div>
                                    </div>


                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 text-gray-500">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-6">
                            {{ $contacts->links('pagination::tailwind') }}
                        </div>
                    </div>
                @endif
            <!-- Pagination -->

        </div>

        <!-- Create Modal -->
        <div x-show="openCreate" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-lg" @click.away="openCreate=false">
                <h3 class="text-lg font-bold mb-4">➕ Add Contact Button </h3>
                <form method="POST" action="{{ route('admin.contacts.store') }}">
                    @csrf
                    @include('admin.contacts.partials.form')
                </form>
            </div>
        </div>
    </div>
@endsection