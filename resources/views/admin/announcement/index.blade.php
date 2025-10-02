@extends('admin.layouts.index')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

    <div class="max-w-7xl mx-auto px-4 py-6">
        {{--Flash messages --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded relative bg-green-100 border border-green-400 text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded relative bg-red-100 border border-red-400 text-red-700">
                {{ session('error') }}
            </div>
        @endif
        <div class="grid grid-cols-2 gap-4 items-center">
            <!-- Left Column -->
            <div class="rounded-lg">
                <h1 class="text-2xl font-bold">Announcements</h1>
            </div>

            <!-- Right Column (Buttons aligned to right) -->
            <div class="flex justify-end items-center space-x-3">

                @if(isset($userPermissions['Announcement']) && in_array('Add', $userPermissions['Announcement']))
                    <a href="{{ route('admin.announcement.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        + Add Announcement
                    </a>
                @endif
                <!-- Whole Settings Modal -->
                <div x-data="{ openSettings: false }">
                    <!-- Trigger button -->
                    @if(isset($userPermissions['Announcement']) && in_array('SaveAnnouncementSetting', $userPermissions['Announcement']))
                        <button @click="openSettings = true" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Open Settings
                        </button>
                    @endif
                    <!-- Modal -->
                    <div x-show="openSettings" x-transition
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

                        <form action="{{ route('admin.announcement.save_settings') }}" method="POST"
                            class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 space-y-6">
                            @csrf

                            <!--Audience checkboxes -->
                            <!-- Audience checkboxes -->

                            @php
                                // Get the saved types from DB, default to empty array if not set
                                $selectedTypes = $settings->settings['audience']['types'] ?? [];
                            @endphp

                            <div>
                                <h2 class="font-semibold mb-2">Audience</h2>

                                <label class="flex items-center space-x-2 mb-1">
                                    <input type="checkbox" name="audience[types][]" value="account_user"
                                        class="rounded border-gray-300" {{ in_array('account_user', $selectedTypes) ? 'checked' : '' }}>
                                    <span>Account User</span>
                                </label>

                                <label class="flex items-center space-x-2 mb-1">
                                    <input type="checkbox" name="audience[types][]" value="account_admin"
                                        class="rounded border-gray-300" {{ in_array('account_admin', $selectedTypes) ? 'checked' : '' }}>
                                    <span>Account Admin</span>
                                </label>

                                <label class="flex items-center space-x-2 mb-1">
                                    <input type="checkbox" name="audience[types][]" value="agency_user"
                                        class="rounded border-gray-300" {{ in_array('agency_user', $selectedTypes) ? 'checked' : '' }}>
                                    <span>Agency User</span>
                                </label>

                                <label class="flex items-center space-x-2 mb-1">
                                    <input type="checkbox" name="audience[types][]" value="agency_admin"
                                        class="rounded border-gray-300" {{ in_array('agency_admin', $selectedTypes) ? 'checked' : '' }}>
                                    <span>Agency Admin</span>
                                </label>
                            </div>




                            <!-- Stop conditions -->
                            <div>
                                <h2 class="font-semibold mb-2">Stop Conditions</h2>
                                <label class="block">
                                    <input type="radio" name="stop" value="never"
                                        @if(($settings->settings['conditions']['stop'] ?? '') === 'never') checked @endif>
                                    Never stop displaying
                                </label>
                                <label class="block">
                                    <input type="radio" name="stop" value="never_show_again"
                                        @if(($settings->settings['conditions']['stop'] ?? '') === 'never_show_again') checked
                                        @endif>
                                    Stop after "Never Show Again"
                                </label>
                                <label class="block">
                                    <input type="radio" name="stop" value="after_views"
                                        @if(($settings->settings['conditions']['stop'] ?? '') === 'after_views') checked
                                        @endif>
                                    Stop after
                                    <input type="number" name="views"
                                        value="{{ $settings->settings['conditions']['views'] ?? 1 }}"
                                        class="border w-16 text-center">
                                    views
                                </label>
                            </div>

                            <!--  Frequency -->
                            <div>
                                <h2 class="font-semibold mb-2">Frequency</h2>
                                <label class="block">
                                    <input type="radio" name="freq" value="every_page"
                                        @if(($settings->settings['frequency']['mode'] ?? '') === 'every_page') checked @endif>
                                    Every page
                                </label>
                                <label class="block">
                                    <input type="radio" name="freq" value="once_session"
                                        @if(($settings->settings['frequency']['mode'] ?? '') === 'once_session') checked
                                        @endif>
                                    Once per session
                                </label>
                                <label class="block">
                                    <input type="radio" name="freq" value="custom"
                                        @if(($settings->settings['frequency']['mode'] ?? '') === 'custom') checked @endif>
                                    Once every
                                    <input type="number" name="freq_value"
                                        value="{{ $settings->settings['frequency']['value'] ?? 1 }}"
                                        class="border w-16 text-center">
                                    <select name="freq_unit" class="border rounded">
                                        <option value="days" @if(($settings->settings['frequency']['unit'] ?? '') === 'days')
                                        selected @endif>
                                            days
                                        </option>
                                        <option value="hours" @if(($settings->settings['frequency']['unit'] ?? '') === 'hours') selected @endif>
                                            hours
                                        </option>
                                    </select>
                                </label>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="openSettings = false"
                                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded flex items-center justify-center gap-2"
                                    onclick="this.querySelector('.spinner').classList.remove('hidden'); this.querySelector('.btn-text').classList.add('hidden');">
                                    <span class="btn-text">Save Settings</span>
                                    <svg class="spinner hidden animate-spin h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                          0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>

                            </div>
                        </form>
                    </div>
                </div>


                <div x-data="{ emailSettings: {{ $errors->any() ? 'true' : 'false' }} }">
                    <!-- Trigger button -->
                    <button @click="emailSettings = true" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Email Settings
                    </button>

                    <!-- Modal -->
                    <div x-show="emailSettings" x-transition
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

                        <form action="{{ route('admin.announcement_email.update') }}" method="POST"
                            class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 space-y-4">
                            @csrf
                            @method('PUT')

                            <h2 class="text-xl font-bold mb-4">Announcement Email Settings</h2>

                            <!-- Global Errors -->
                            @if ($errors->any())
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- From Name -->
                            <div>
                                <label class="block font-medium mb-1">From Name</label>
                                <input type="text" name="from_name"
                                    value="{{ old('from_name', $settings->from_name ?? '') }}"
                                    class="w-full border-gray-300 rounded px-4 py-2 @error('from_name') border-red-500 @enderror">

                                @error('from_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- From Email -->
                            <div>
                                <label class="block font-medium mb-1">From Email</label>
                                <input type="email" name="from_email"
                                    value="{{ old('from_email', $settings->from_email ?? '') }}"
                                    class="w-full border-gray-300 rounded px-4 py-2 @error('from_email') border-red-500 @enderror">

                                @error('from_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Main Location ID -->
                            <div>
                                <label class="block font-medium mb-1">Main Location ID</label>
                                <input type="text" name="location_id"
                                    value="{{ old('location_id', $settings->location_id ?? '') }}"
                                    class="w-full border-gray-300 rounded px-4 py-2 @error('location_id') border-red-500 @enderror">

                                @error('location_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priviet Integration Key -->
                            <div>
                                <label class="block font-medium mb-1">Priviet Integration Key</label>
                                <input type="text" name="priviet_key"
                                    value="{{ old('priviet_key', $settings->priviet_key ?? '') }}"
                                    class="w-full border-gray-300 rounded px-4 py-2 @error('priviet_key') border-red-500 @enderror">

                                @error('priviet_key')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3 mt-4">
                                <button type="button" @click="emailSettings = false"
                                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded flex items-center justify-center gap-2"
                                    onclick="this.querySelector('.spinner').classList.remove('hidden'); this.querySelector('.btn-text').classList.add('hidden');">
                                    <span class="btn-text">Save</span>
                                    <svg class="spinner hidden animate-spin h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                      0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>



            </div>
        </div>
        {{-- Header --}}
        @if(isset($userPermissions['Announcement']) && in_array('List', $userPermissions['Announcement']))

            {{-- Table --}}
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                    <thead class="bg-gray-100 text-left text-xs uppercase font-semibold text-gray-600">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Expiry</th>
                            <!-- <th class="px-6 py-3">Location</th> -->
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td class="px-6 py-4">{{ $announcement->title }}</td>
                                <td class="px-6 py-4 capitalize">{{ $announcement->status }}</td>
                                <td class="px-6 py-4">
                                    @if($announcement->expiry_type === 'never')
                                        Never
                                    @elseif($announcement->expiry_type === 'date')
                                        {{ \Carbon\Carbon::parse($announcement->expiry_date)->format('d M Y') }}
                                    @endif
                                </td>
                                <!-- <td class="px-6 py-4">{{ $announcement->user_id }}</td> -->
                                <td class="px-6 py-4 text-right space-x-2">
                                    {{-- Edit --}}
                                    @if(isset($userPermissions['Announcement']) && in_array('Edit', $userPermissions['Announcement']))
                                        <a href="javascript:void(0)" onclick='openEditModal(@json($announcement))'
                                            class="bg-yellow-400 text-white px-3 py-1 rounded inline-block">
                                            <i class="far fa-edit" style="font-size:14px"></i>
                                        </a>
                                    @endif
                                    @if(isset($userPermissions['Announcement']) && in_array('Delete', $userPermissions['Announcement']))
                                        {{-- Delete --}}
                                        <form action="{{ route('admin.destroy.announcement', $announcement->id) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">
                                                <i class="fa fa-trash" style="font-size:14px"></i>
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Settings --}}

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $announcements->links('pagination::tailwind') }}
            </div>

        @endif
    </div>


    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Edit Announcement</h2>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full border-gray-300 rounded px-4 py-2">
                        <option value="active">Active</option>
                        <option value="push">Push</option>
                    </select>
                </div>

                <!-- Expiry Type -->
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Expiry Type</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="expiry_type" value="never" id="edit_expiry_never" class="mr-2">
                                Never
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="expiry_type" value="date" id="edit_expiry_date" class="mr-2">
                                Expire on Date
                            </label>
                        </div>
                    </div>
                    <div id="editExpiryDateDiv" class="hidden">
                        <label class="block text-sm font-medium mb-1">Expiry Date</label>


                        <input type="date" name="expiry_date" id="edit_expiry_input"
                            class="w-full border-gray-300 rounded px-4 py-2">
                    </div>
                </div>

                <!-- Audience Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Target Audience</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="audience_type" value="all" id="edit_audience_all" class="mr-2">
                            All Users
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="audience_type" value="specific" id="edit_audience_specific"
                                class="mr-2">
                            Specific Users
                        </label>
                    </div>
                </div>

                <!-- Specific Users -->
                <div id="editSpecificUsersDiv" class="mb-4 hidden">
                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Enter Location IDs (comma separated)</label>
                            <input type="text" id="edit_location_id" name="location_ids[]"
                                class="w-full px-4 py-2 border-gray-300 rounded">

                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Enter Emails (comma separated)</label>
                            <input type="text" id="edit_user_emails" name="emails[]"
                                class="w-full px-4 py-2 border-gray-300 rounded">

                        </div>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="title" id="edit_title" class="w-full border-gray-300 rounded px-4 py-2">
                    </div>
                    <div>
                        <div class="mt-8 flex items-center">
                           <input type="checkbox" id="edit_use_general_settings" name="use_general_settings"
                                class="w-5 h-5 text-blue-600 mr-2" @if($settings && data_get($settings->settings, 'general_settings'))
                                                checked
                                            @endif>
                            <label for="edit_use_general_settings" class="text-sm font-medium">Use General Settings</label>
                        </div>

                    </div>
                </div>
                <!-- General Settings Checkbox -->

                <!-- Custom Settings Fields (Audience & Frequency) -->
                <div id="edit_customSettingsFields">
                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Audience -->
                        <div class="mb-4">
                            <h2 class="font-semibold mb-2">Audience</h2>
                            <label class="flex items-center space-x-2 mb-1">
                                <input type="checkbox" name="audience[types][]" value="account_user"
                                    id="edit_audience_account_user" class="rounded border-gray-300">
                                <span>Account User</span>
                            </label>
                            <label class="flex items-center space-x-2 mb-1">
                                <input type="checkbox" name="audience[types][]" value="account_admin"
                                    id="edit_audience_account_admin" class="rounded border-gray-300">
                                <span>Account Admin</span>
                            </label>
                            <label class="flex items-center space-x-2 mb-1">
                                <input type="checkbox" name="audience[types][]" value="agency_user"
                                    id="edit_audience_agency_user" class="rounded border-gray-300">
                                <span>Agency User</span>
                            </label>
                            <label class="flex items-center space-x-2 mb-1">
                                <input type="checkbox" name="audience[types][]" value="agency_admin"
                                    id="edit_audience_agency_admin" class="rounded border-gray-300">
                                <span>Agency Admin</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Display Setting</label>
                            <select name="display_setting" id="edit_display_setting"
                                class="w-full border-gray-300 rounded px-4 py-2"
                                onchange="toggleCustomViewsEdit(this.value)">
                                <option value="never_again">Never Again</option>
                                <option value="custom">Stop After X Views</option>
                            </select>
                            <input type="number" name="custom_views" id="edit_custom_views"
                                placeholder="Enter number of views"
                                class="border rounded-lg w-10 mt-4 p-0 text-center hidden">
                        </div>


                        <!-- Custom Views Input -->

                        <!-- Frequency -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Frequency</h3>
                            <label class="block flex space-x-2 mb-1">
                                <input type="radio" name="freq" class="mr-3" value="every_page" id="edit_freq_every_page">
                                Every page
                            </label>
                            <label class="block flex space-x-2 mb-1">
                                <input type="radio" name="freq" class="mr-3" value="once_session"
                                    id="edit_freq_once_session">
                                Once per session
                            </label>
                            <label class="block flex items-center space-x-2">
                                <input type="radio" name="freq" value="custom" id="edit_freq_custom">
                                <span>Once every</span>
                                <input type="number" name="freq_value" id="edit_freq_value"
                                    class="border rounded-lg w-10 p-0 text-center">
                                <select name="freq_unit" id="edit_freq_unit" class="border rounded">
                                    <option value="days">days</option>
                                    <option value="hours">hours</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Body</label>
                    <textarea name="body" id="edit_body" rows="4"
                        class="w-full border-gray-300 rounded px-4 py-2 resize-none"></textarea>
                </div>

                <!-- Email -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="allow_email" value="1" id="edit_allow_email"
                        class="w-5 h-5 text-blue-600 mr-2">
                    <label class="text-sm font-medium">Allow Email</label>
                </div>

                <!-- Submit -->
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-6 py-2 rounded">Cancel</button>
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded flex items-center justify-center gap-2"
                        onclick="this.querySelector('.spinner').classList.remove('hidden'); this.querySelector('.btn-text').classList.add('hidden');">
                        <span class="btn-text">Update</span>
                        <svg class="spinner hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                              0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Initialize Tagify once DOM loaded
            // const locationInput = document.querySelector('#edit_location_id');
            // const tagifyLocation = new Tagify(locationInput, { 
            //     delimiters: ",",
            //      maxTags: 50
            //      });

            // const emailsInput = document.querySelector('#edit_user_emails');
            // const tagifyEmails = new Tagify(emailsInput, {
            //     delimiters: ",",
            //     validate: function (email) {
            //         return true;
            //     }
            // });

            var tagifyLocation = new Tagify(document.querySelector('#edit_location_id'), {
                delimiters: ",",
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(",")
            });

            // Emails
            var tagifyEmails = new Tagify(document.querySelector('#edit_user_emails'), {
                delimiters: ",",
                validate: email => true, // accept all emails
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(",")
            });

            // Full page loader for all forms
            const loader = document.getElementById("formLoader");
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", () => loader.classList.remove("hidden"));
            });

            // Edit Modal Logic
            window.openEditModal = function (announcement) {
                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                form.action = "{{ route('admin.announcement.update', ['id' => ':id']) }}".replace(':id', announcement.id);

                // Basic fields
                document.getElementById('edit_title').value = announcement.title;
                document.getElementById('edit_body').value = announcement.body;
                document.getElementById('edit_status').value = announcement.status;

                // Display setting
                const displaySelect = document.getElementById('edit_display_setting');
                const customInput = document.getElementById('edit_custom_views');
                if (announcement.display_setting.startsWith("stop_after_")) {
                    displaySelect.value = "custom";
                    const match = announcement.display_setting.match(/stop_after_(\d+)_view/);
                    customInput.value = match ? match[1] : '';
                    customInput.classList.remove("hidden");
                } else {
                    displaySelect.value = announcement.display_setting;
                    customInput.value = '';
                    customInput.classList.add("hidden");
                }

                // Audience type
                if (announcement.audience_type === "specific") {
                    document.getElementById('edit_audience_specific').checked = true;
                    document.getElementById('editSpecificUsersDiv').classList.remove('hidden');

                    // Update Tagify values
                    tagifyLocation.removeAllTags();
                    tagifyEmails.removeAllTags();
                    if (announcement.locations) {
                        tagifyLocation.addTags(announcement.locations.map(l => l.location_id).filter(Boolean));
                        tagifyEmails.addTags(announcement.locations.map(l => l.email).filter(Boolean));
                    }
                } else {
                    document.getElementById('edit_audience_all').checked = true;
                    document.getElementById('editSpecificUsersDiv').classList.add('hidden');
                }

                // Expiry type
                if (announcement.expiry_type === "date") {
                    document.getElementById('edit_expiry_date').checked = true;
                    document.getElementById('editExpiryDateDiv').classList.remove('hidden');
                    document.getElementById('edit_expiry_input').value = announcement.expiry_date ? new Date(announcement.expiry_date).toISOString().split('T')[0] : '';
                } else {
                    document.getElementById('edit_expiry_never').checked = true;
                    document.getElementById('editExpiryDateDiv').classList.add('hidden');
                }

                // Allow email
                document.getElementById('edit_allow_email').checked = !!announcement.allow_email;

                // General settings
                const generalCheckbox = document.getElementById('edit_use_general_settings');
                const customFields = document.getElementById('edit_customSettingsFields');
                if (announcement.settings?.general_settings) {
                    generalCheckbox.checked = true;
                    customFields.classList.add('hidden');
                } else {
                    generalCheckbox.checked = false;
                    customFields.classList.remove('hidden');

                    // Audience checkboxes
                    const types = announcement.settings?.audience_types || [];
                    ['account_user', 'account_admin', 'agency_user', 'agency_admin'].forEach(type => {
                        const el = document.getElementById(`edit_audience_${type}`);
                        if (el) el.checked = types.includes(type);
                    });

                    // Frequency
                    const freq = announcement.settings?.frequency || {};
                    if (freq.type) {
                        const freqEl = document.getElementById(`edit_freq_${freq.type}`);
                        if (freqEl) freqEl.checked = true;
                    }
                    document.getElementById('edit_freq_value').value = freq.value || '';
                    document.getElementById('edit_freq_unit').value = freq.unit || 'days';
                }

                // Show modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            window.closeEditModal = function () {
                const modal = document.getElementById('editModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Toggle custom fields
            document.getElementById('edit_use_general_settings').addEventListener('change', function () {
                document.getElementById('edit_customSettingsFields').classList.toggle('hidden', this.checked);
            });

            // Audience type toggle
            document.getElementById('edit_audience_all').addEventListener('change', () => document.getElementById('editSpecificUsersDiv').classList.add('hidden'));
            document.getElementById('edit_audience_specific').addEventListener('change', () => document.getElementById('editSpecificUsersDiv').classList.remove('hidden'));

            // Expiry type toggle
            document.getElementById('edit_expiry_never').addEventListener('change', () => document.getElementById('editExpiryDateDiv').classList.add('hidden'));
            document.getElementById('edit_expiry_date').addEventListener('change', () => document.getElementById('editExpiryDateDiv').classList.remove('hidden'));

            // Display setting toggle
            document.getElementById('edit_display_setting').addEventListener('change', function () {
                document.getElementById('edit_custom_views').classList.toggle('hidden', this.value !== 'custom');
            });
        });
    </script>
    </script>

    <!-- Full Page Loader -->
    <div id="formLoader" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 hidden">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-white mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                            0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-white text-lg">Saving...</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("formLoader");

            // Har form ke liye listener lagao
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function () {
                    loader.classList.remove("hidden"); // loader show
                });
            });
        });
    </script>

@endsection