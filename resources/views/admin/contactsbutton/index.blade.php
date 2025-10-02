@extends('admin.layouts.index')

@section('content')
    <div x-data="{ openCreate:false, openEdit:null, openDelete:null }" class="p-6">

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-center">📇 Contacts Button</h2>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Button -->
            @if(isset($userPermissions['Contactbutton']) && in_array('Add', $userPermissions['Contactbutton']))
                <div class="text-end mb-4">
                    <button @click="openCreate = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        + Add Contact Button
                    </button>
                </div>
            @endif
            <!-- Table -->
            <div class="overflow-x-auto">
                @if(isset($userPermissions['Contactbutton']) && in_array('List', $userPermissions['Contactbutton']))
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
                                    <tr x-data="{ openEdit:false, openCreate:false, openDelete:false">
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
                                            @if(isset($userPermissions['Contactbutton']) && in_array('Edit', $userPermissions['Contactbutton']))
                                                <button @click="openEdit={{ $c->id }}"
                                                    class="px-3 py-1 bg-yellow-400 text-white rounded">✏️</button>
                                            @endif
                                            @if(isset($userPermissions['Contactbutton']) && in_array('Delete', $userPermissions['Contactbutton']))
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
                                                @include('admin.contactsbutton.partials.form', ['contact' => $c, 'editMode' => true])
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
                <h3 class="text-lg font-bold mb-4">➕ Add Contact Button</h3>
                <form method="POST" action="{{ route('admin.contacts.store') }}">
                    @csrf
                    @include('admin.contactsbutton.partials.form', ['editMode' => false])
                </form>
            </div>
        </div>

    </div>

     <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("formLoader");
            const loaderText = loader.querySelector("span");
        
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function () {
                    // Default text
                    // loaderText.textContent = "...";
        
                    // Agar delete form hai
                    const methodInput = form.querySelector("input[name='_method']");
                    if (methodInput && methodInput.value === "DELETE") {
                        // loaderText.textContent = "...";
                    }
        
                    loader.classList.remove("hidden"); // loader show
                });
            });
        });
        </script>
     <div id="formLoader" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 hidden">
    <div class="flex flex-col items-center">
        <svg class="animate-spin h-10 w-10 text-white mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-white text-lg">......</span>
    </div>
@endsection