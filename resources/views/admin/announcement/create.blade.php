@extends('admin.layouts.index')

@section('content')
@if ($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
        <strong class="font-bold">⚠️ Validation Error!</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Create Announcement</h1>
        <a href="{{ route('admin.announcement.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            &larr; Back
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.announcements.store') }}" method="POST">
            @csrf

            {{-- Status --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300">
                    <option value="active">Active</option>
                    <option value="push">Push</option>
                </select>
            </div>

            {{-- Expiry Type --}}
            <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Expiry Type</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="expiry_type" value="never" checked class="mr-2 border-gray-300 focus:ring-blue-300"> Never
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="expiry_type" value="date" class="mr-2 border-gray-300 focus:ring-blue-300"> Expire on Date
                        </label>
                    </div>
                </div>

                <div id="expiryDateDiv" class="hidden">
                    <label class="block text-sm font-medium mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300">
                </div>
            </div>

            {{-- Audience Type --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Target Audience</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="audience_type" value="all" checked class="mr-2 border-gray-300 focus:ring-blue-300"> All Users
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="audience_type" value="specific" class="mr-2 border-gray-300 focus:ring-blue-300"> Specific Users
                    </label>
                </div>
            </div>

            {{-- Specific Users --}}
            <div id="specificUsersDiv" class="mb-4 hidden">
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Enter Location IDs</label>
                        <input type="text" id="location_id" name="location_ids[]" placeholder="Enter location ids" class="w-full px-4 py-2 border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Enter Emails</label>
                        <input name="emails[]" id="user_emails" placeholder="Enter emails" class="w-full px-4 py-2 border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300">
                    </div>
                </div>
            </div>

            {{-- Title + Display Setting --}}
            <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Display Setting</label>
                    <select id="display_setting" name="display_setting" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300">
                        <option value="never_again">Never Again</option>
                        <option value="custom">Stop After X Views</option>
                    </select>
                    <input type="number" id="custom_views" name="custom_views" class="w-full border-gray-300 rounded px-4 py-2 mt-2 hidden" placeholder="Enter number of views" min="1">
                </div>
            </div>

            {{-- General Settings Checkbox --}}
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="use_general_settings" name="use_general_settings" class="w-5 h-5 text-blue-600 mr-2">
                <label for="use_general_settings" class="text-sm font-medium">Use General Settings</label>
            </div>

            {{-- Custom Settings Fields --}}
            <div id="customSettingsFields">

             <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Audience --}}
                <div class="mb-4">
                    <h2 class="font-semibold mb-2">Audience</h2>
                    <label class="flex items-center space-x-2 mb-1">
                        <input type="checkbox" name="audience[types][]" value="account_user" class="rounded border-gray-300"> <span>Account User</span>
                    </label>
                    <label class="flex items-center space-x-2 mb-1">
                        <input type="checkbox" name="audience[types][]" value="account_admin" class="rounded border-gray-300"> <span>Account Admin</span>
                    </label>
                    <label class="flex items-center space-x-2 mb-1">
                        <input type="checkbox" name="audience[types][]" value="agency_user" class="rounded border-gray-300"> <span>Agency User</span>
                    </label>
                    <label class="flex items-center space-x-2 mb-1">
                        <input type="checkbox" name="audience[types][]" value="agency_admin" class="rounded border-gray-300"> <span>Agency Admin</span>
                    </label>
                </div>

                {{-- Stop Conditions --}}
                <!-- <div class="mb-4">
                    <h3 class="font-semibold mb-2">Stop Conditions</h3>
                    <label class="block flex space-x-2 mb-1">
                        <input type="radio" name="stop" value="never"> Never stop displaying
                    </label>
                    <label class="block flex space-x-2 mb-1">
                        <input type="radio" name="stop" value="never_show_again"> Stop after "Never Show Again"
                    </label>
                    <label class="block flex items-center space-x-2">
                        <input type="radio" name="stop" value="after_views"> <span>Stop after</span>
                        <input type="number" name="views" class="border rounded w-20 text-center"> <span>views</span>
                    </label>
                </div> -->

                {{-- Frequency --}}
                <div class="mb-4">
                    <h3 class="font-semibold mb-2">Frequency</h3>
                    <label class="block flex space-x-2 mb-1">
                        <input type="radio" name="freq" value="every_page"> Every page
                    </label>
                    <label class="block flex space-x-2 mb-1">
                        <input type="radio" name="freq" value="once_session"> Once per session
                    </label>
                    <label class="block flex items-center space-x-2">
                        <input type="radio" name="freq" value="custom"> <span>Once every</span>
                        <input type="number" name="freq_value" value="1" class="border rounded w-20 text-center">
                        <select name="freq_unit" class="border rounded">
                            <option value="days">days</option>
                            <option value="hours">hours</option>
                        </select>
                    </label>
                </div>

             </div>
            </div>

            {{-- Body --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Body</label>
                <textarea name="body" rows="4" class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300 resize-none" required></textarea>
            </div>

            {{-- Email --}}
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="allow_email" value="1" class="w-5 h-5 text-blue-600 mr-2">
                <label class="text-sm font-medium">Allow Email</label>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">Save Announcement</button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tagify
    var input = document.querySelector('#user_emails');
    new Tagify(input, { delimiters: ",| ", pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, maxTags: 20, dropdown:{enabled:0}, editTags:true, duplicates:false });
    
    var locationInput = document.querySelector('#location_id');
    new Tagify(locationInput, { delimiters: ",| ", maxTags: 20 });

    // Show/hide specific users
    document.querySelectorAll('input[name="audience_type"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            document.getElementById('specificUsersDiv').classList.toggle('hidden', this.value !== 'specific');
        });
    });

    // Show/hide expiry date
    document.querySelectorAll('input[name="expiry_type"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            document.getElementById('expiryDateDiv').classList.toggle('hidden', this.value !== 'date');
        });
    });

    // Show/hide custom views input
    document.getElementById('display_setting').addEventListener('change', function() {
        let input = document.getElementById('custom_views');
        if(this.value === 'custom') input.classList.remove('hidden');
        else input.classList.add('hidden');
    });

    // General Settings
    const generalCheckbox = document.getElementById('use_general_settings');
    const customFields = document.getElementById('customSettingsFields');
    generalCheckbox.addEventListener('change', function() {
        customFields.classList.toggle('hidden', this.checked);
    });
});
</script>
@endsection
