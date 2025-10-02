@extends('admin.layouts.index')

@section('content')
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            <strong class="font-bold">⚠️ Validation Error!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-6xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Create Announcement</h1>
            <a href="{{ route('admin.announcement.index') }}"
                class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                &larr; Back
            </a>
        </div>
<div x-data="{ loading: false }">
        <!-- Card -->
        <div class="bg-white shadow-lg rounded-xl p-8">
            <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Status -->
                <div x-data="{ open: false, selected: 'Active' }" class="relative w-64">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <button type="button" @click="open = !open"
                        class="w-full flex justify-between items-center border border-gray-300 bg-white rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span x-text="selected"></span>
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-10">
                        <ul>
                            <li>
                                <button type="button" @click="selected = 'Active'; open=false"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100">✅ Active</button>
                            </li>
                            <li>
                                <button type="button" @click="selected = 'Push'; open=false"
                                    class="w-full text-left px-4 py-2 bg-red-500 text-white hover:bg-red-600">🚨
                                    Push</button>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="status" :value="selected">
                </div>

                <!-- Expiry Type -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Expiry Type</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="radio" name="expiry_type" value="never" checked
                                    class="mr-2 text-blue-600 focus:ring-blue-500"> Never
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="expiry_type" value="date"
                                    class="mr-2 text-blue-600 focus:ring-blue-500"> Expire on Date
                            </label>
                        </div>
                    </div>
                    <div id="expiryDateDiv" class="hidden">
                        <label class="block text-sm font-semibold mb-2">Expiry Date</label>
                        <input type="date" name="expiry_date"
                            class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Audience Type -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Target Audience</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="radio" name="audience_type" value="all" checked
                                class="mr-2 text-blue-600 focus:ring-blue-500"> All Users
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="audience_type" value="specific"
                                class="mr-2 text-blue-600 focus:ring-blue-500"> Specific Users
                        </label>
                    </div>
                </div>

                <!-- Specific Users -->
                <div id="specificUsersDiv" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Enter Location IDs</label>
                        <input type="text" id="location_id" name="location_ids[]" placeholder="Enter location ids"
                            class="w-full px-4 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Enter Emails</label>
                        <input type="text" id="user_emails" name="emails[]" placeholder="Enter emails"
                            class="w-full px-4 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- General Settings -->
                <div class="flex items-center">
                    <input type="checkbox" id="use_general_settings" name="use_general_settings"
                        class="w-5 h-5 text-blue-600 mr-2">
                    <label for="use_general_settings" class="text-sm font-medium">Use General Settings</label>
                </div>

                <!-- Custom Settings -->
                <div id="customSettingsFields" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Display Setting -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">Display Setting</label>
                            <select id="display_setting" name="display_setting"
                                class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="never_again">Never Again</option>
                                <option value="custom">Stop After X Views</option>
                            </select>
                            <input type="number" id="custom_views" name="custom_views"
                                class="w-full border-gray-300 rounded-lg px-4 py-2 mt-2 hidden"
                                placeholder="Enter number of views" min="1">
                        </div>

                        <!-- Audience -->
                        <div>
                            <h2 class="font-semibold mb-2">Audience</h2>
                            <div class="space-y-2">
                                @foreach (['account_user', 'account_admin', 'agency_user', 'agency_admin'] as $aud)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="audience[types][]" value="{{ $aud }}"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="capitalize">{{ str_replace('_', ' ', $aud) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Frequency -->
                    <div>
                        <h3 class="font-semibold mb-2">Frequency</h3>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="freq" value="every_page" class="text-blue-600"> Every page
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="freq" value="once_session" class="text-blue-600"> Once per session
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="freq" value="custom" class="text-blue-600">
                                <span>Once every</span>
                                <input type="number" name="freq_value" value="1" class="border rounded-lg w-20 text-center">
                                <select name="freq_unit" class="border rounded-lg">
                                    <option value="days">days</option>
                                    <option value="hours">hours</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Title</label>
                    <input type="text" name="title"
                        class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Body -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Body</label>
                    <textarea name="body" rows="4"
                        class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 resize-none"
                        required></textarea>
                </div>

                <!-- Allow Email -->
                <div class="flex items-center">
                    <input type="checkbox" name="allow_email" value="1" class="w-5 h-5 text-blue-600 mr-2">
                    <label class="text-sm font-medium">Allow Email</label>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button x-on:click="loading = true; $el.form.submit()" :disabled="loading"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <!-- Full Page Loader -->
        <div x-show="loading" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50">
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
</div>
        {{-- Scripts --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new Tagify(document.querySelector('#user_emails'), {
                    delimiters: ",| ", pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, maxTags: 20
                });
                new Tagify(document.querySelector('#location_id'), { delimiters: ",| ", maxTags: 20 });

                document.querySelectorAll('input[name="audience_type"]').forEach((radio) => {
                    radio.addEventListener('change', function () {
                        document.getElementById('specificUsersDiv').classList.toggle('hidden', this.value !== 'specific');
                    });
                });

                document.querySelectorAll('input[name="expiry_type"]').forEach((radio) => {
                    radio.addEventListener('change', function () {
                        document.getElementById('expiryDateDiv').classList.toggle('hidden', this.value !== 'date');
                    });
                });

                document.getElementById('display_setting').addEventListener('change', function () {
                    document.getElementById('custom_views').classList.toggle('hidden', this.value !== 'custom');
                });

                const generalCheckbox = document.getElementById('use_general_settings');
                const customFields = document.getElementById('customSettingsFields');
                generalCheckbox.addEventListener('change', function () {
                    customFields.classList.toggle('hidden', this.checked);
                });
            });
        </script>
@endsection