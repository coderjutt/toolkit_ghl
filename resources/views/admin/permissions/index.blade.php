@extends('admin.layouts.index')

@section('content')
    <div class="max-w-12xl mx-auto px-4 py-6">

        <!-- Alpine.js include -->



        <!-- Header Section -->
        <div class="grid grid-cols-1 gap-6 mb-6">
            <!-- <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-2xl font-bold">Assign User Permissions</h2>
                </div> -->

            <div class="flex justify-end items-center">


             <div x-data="{ open: false }">
                        <button @click="open = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 mx-2 rounded-lg shadow">
                            Add Modules
                        </button>
                        <div x-show="open" x-transition
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

                            <div @click.away="open = false" class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 relative">
                                <!-- Close Button -->
                                <button @click="open = false"
                                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-lg font-bold">
                                    ✕
                                </button>

                                <h2 class="text-xl font-bold mb-4">Create Module</h2>

                                <form method="POST" action="{{ route('admin.modules.store') }}">
                                    @csrf

                                    <!-- Module Name -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Module Name</label>
                                        <input type="text" name="name"
                                            value="{{ old('name') }}"
                                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 p-2 @error('name') border-red-500 @enderror"
                                            required>
                                        @error('name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Permissions -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            
                                            @foreach(['Add', 'Edit', 'Delete', 'List'] as $perm)
                                            
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                        {{ (is_array(old('permissions')) && in_array($perm, old('permissions'))) ? 'checked' : '' }}>
                                                    <span class="capitalize">{{ $perm }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('permissions')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-4">
                                        <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow">
                                            Create Module
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


            </div>
        </div>

    </div>

    {{-- User Dropdown --}}
    <div class="mb-6">
        <label for="user_id" class="block text-md font-semibold mb-2">Select User</label>
        <select id="user_id" class="w-full border border-gray-300 rounded px-4 py-2">
            <option value="">-- Choose User --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Permission Form --}}
    <form id="permissionForm">
        @csrf
        <input type="hidden" name="user_id" id="selectedUserId">

        {{-- Module Buttons --}}
        <div class="flex justify-end space-x-4 mb-4">
            <button type="button" onclick="toggleAllPermissions(true)"
                class="px-4 py-2 bg-green-600 text-white rounded">Check All Modules</button>
            <button type="button" onclick="toggleAllPermissions(false)"
                class="px-4 py-2 bg-red-600 text-white rounded">Uncheck All Modules</button>
        </div>

        {{-- Main Permissions --}}
       <!-- Module List -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="module-permissions">
                @foreach ($modules as $module)
                    <div class="border border-gray-300 rounded-lg shadow-sm">
                        <!-- Module Name -->
                        <div class="bg-blue-100 p-3 cursor-pointer font-semibold" onclick="toggleBox('{{ $module->name }}')">
                            {{ $module->name }}
                        </div>

                        <!-- Module Permissions -->
                        <div class="p-4 bg-blue-50 hidden" id="box-{{ $module->name }}">
                            @foreach ($module->permissions as $perm)
                                <div class="mb-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox text-blue-600" name="permissions[]"
                                            value="{{ $module->name . '.' . $perm }}">
                                        <span class="ml-2">{{ $perm }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>



        {{-- Script Permissions Section --}}
        <div class="mt-10 border-t pt-6">
            <h3 class="text-xl font-bold text-green-600 mb-4">Script Permissions</h3>

            <div class="flex justify-end space-x-4 mb-4">
                <button type="button" onclick="toggleScriptPermissions(true)"
                    class="px-4 py-2 bg-green-600 text-white rounded">Check All Scripts</button>
                <button type="button" onclick="toggleScriptPermissions(false)"
                    class="px-4 py-2 bg-red-600 text-white rounded">Uncheck All Scripts</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $scriptPermissionsList = [
                        'User',
                        'Rename Menus',
                        'Reorder',
                        'Custom Menu',
                        'Custom Menu Link',
                        'Menu Data',
                        'User Custom Menu Link',
                        'Permissions',
                    ];
                @endphp

                @foreach ($scriptPermissionsList as $perm)
                    <div class="flex items-center">
                        <input type="checkbox" name="script_permissions[]" value="{{ $perm }}"
                            class="form-checkbox text-blue-600">
                        <label class="ml-2">{{ $perm }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-8">
            <button type="button" onclick="submitPermissions()"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Save Permissions
            </button>
        </div>
    </form>
    </div>

    {{-- JavaScript --}}
<script>
    function toggleBox(id) {
        const box = document.getElementById('box-' + id);
        if (box) {
            box.classList.toggle('hidden'); // ✅ Tailwind hidden toggle
        }
    }

    function toggleAllPermissions(check) {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = check);
    }

    function toggleScriptPermissions(check) {
        document.querySelectorAll('input[name="script_permissions[]"]').forEach(cb => cb.checked = check);
    }

    function submitPermissions() {
        const userId = document.getElementById('user_id').value;
        const permissions = [];
        const scriptPermissions = [];

        document.querySelectorAll('input[name="permissions[]"]:checked').forEach(el => {
            permissions.push(el.value);
        });

        document.querySelectorAll('input[name="script_permissions[]"]:checked').forEach(el => {
            scriptPermissions.push(el.value);
        });

        fetch("{{ route('admin.permissions.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId,
                permissions: permissions,
                script_permissions: scriptPermissions,
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Permissions saved successfully.');
                } else {
                    alert('Failed to save permissions.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong.');
            });
    }

    // On user change
    document.getElementById('user_id').addEventListener('change', function () {
        const userId = this.value;
        if (!userId) {
            window.location.reload();
            return;
        }

        document.getElementById('selectedUserId').value = userId;

        fetch("{{ route('admin.permissions.get', '') }}/" + userId)
            .then(res => res.json())
            .then(data => {
                // Uncheck all first
                document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

                // Check main permissions
                Object.entries(data.permissions).forEach(([module, perms]) => {
                    perms.forEach(perm => {
                        const value = `${module}.${perm}`;
                        const cb = document.querySelector(
                            `input[type="checkbox"][name="permissions[]"][value="${value}"]`
                        );
                        if (cb) cb.checked = true;
                    });
                });

                // Check script permissions
                data.script_permissions.forEach(p => {
                    const cb = document.querySelector(
                        `input[type="checkbox"][name="script_permissions[]"][value="${p}"]`);
                    if (cb) cb.checked = true;
                });

                // Auto-open boxes for modules that have permissions
                Object.keys(data.permissions).forEach(module => {
                    const box = document.getElementById('box-' + module);
                    if (box) {
                        box.classList.remove('hidden'); // ✅ show module permissions
                    } else {
                        console.warn("Box not found for module:", module);
                    }
                });
            })
            .catch(err => console.error('Permission fetch error:', err));
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection