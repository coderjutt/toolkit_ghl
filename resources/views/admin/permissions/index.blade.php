@extends('admin.layouts.index')

@section('content')
<div class="max-w-12xl mx-auto px-4 py-6">

    <!-- Header Section -->
    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="flex justify-end items-center">
            <div x-data="{ open: false }">
                <button @click="open = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 mx-2 rounded-lg shadow">
                    Add Modules
                </button>
                <div x-show="open" x-transition
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div @click.away="open = false"
                        class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 relative">

                        <!-- Close Button -->
                        <button @click="open = false"
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-lg font-bold">
                            ✕
                        </button>

                        <h2 class="text-xl font-bold mb-4">Create Module</h2>

                        <form method="POST" action="{{ route('admin.modules.store') }}"
                            x-data="{ type: '{{ old('type', 'non-scripted') }}' }">
                            @csrf

                            <!-- Module Name -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Module Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 p-2 @error('name') border-red-500 @enderror"
                                    required>
                                @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Module Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Module Type</label>
                                <select name="type" x-model="type"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 p-2">
                                    <option value="scripted">Scripted</option>
                                    <option value="non-scripted">Non Scripted</option>
                                </select>
                                @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Permissions -->
                            <div class="mb-4" x-show="type === 'non-scripted'">
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
                                    <span class="btn-text">Create Module</span>
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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="module-permissions">
        @foreach ($nonScriptedModules as $module)
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
            @foreach ($scriptedModules as $module)
            <div class="flex items-center">
                <input type="checkbox" name="script_permissions[]" value="{{ $module->name }}"
                    class="form-checkbox text-blue-600"
                    {{ in_array($module->name, $savedScriptPermissions) ? 'checked' : '' }}>
                <label class="ml-2">{{ $module->name }}</label>
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

<!-- Loader Overlay -->
<div id="loaderOverlay"
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="w-16 h-16 border-4 border-blue-600 border-dashed rounded-full animate-spin"></div>
</div>

<!-- Global Form Loader -->
<div id="formLoader" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 hidden">
    <div class="flex flex-col items-center">
        <svg class="animate-spin h-10 w-10 text-white mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 
                                                                0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-white text-lg">Saving...</span>
    </div>
</div>

{{-- JavaScript --}}
<script>
    function showLoader() {
        document.getElementById('loaderOverlay').classList.remove('hidden');
    }

    function hideLoader() {
        document.getElementById('loaderOverlay').classList.add('hidden');
    }

    function toggleBox(id) {
        const box = document.getElementById('box-' + id);
        if (box) {
            box.classList.toggle('hidden');
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

        showLoader(); // ✅ Show loader on save

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
                hideLoader(); // ✅ Hide loader
                if (data.success) {
                    alert('Permissions saved successfully.');
                    window.location.reload();
                } else {
                    alert('Failed to save permissions.');
                }
            })
            .catch(err => {
                hideLoader(); // ✅ Hide loader
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

        showLoader(); // ✅ Show loader when fetching

        fetch("{{ route('admin.permissions.get', '') }}/" + userId)
            .then(res => res.json())
            .then(data => {
                hideLoader(); // ✅ Hide loader after fetch

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
                        box.classList.remove('hidden');
                    }
                });
            })
            .catch(err => {
                hideLoader(); // ✅ Hide loader on error
                console.error('Permission fetch error:', err);
            });
    });

    // Global loader on all forms (including Create Module)
    document.addEventListener("DOMContentLoaded", function () {
        const loader = document.getElementById("formLoader");
        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("submit", function () {
                loader.classList.remove("hidden"); // ✅ Show overlay loader
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
