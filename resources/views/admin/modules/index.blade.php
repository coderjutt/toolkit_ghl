@extends('admin.layouts.index')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Users Module</h1>
        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add User
        </a>
    </div>

    {{-- Example Table --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
            <thead class="bg-gray-100 text-left text-xs uppercase font-semibold text-gray-600">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Role</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                {{-- Example row --}}
                <tr>
                    <td class="px-6 py-4">1</td>
                    <td class="px-6 py-4">John Doe</td>
                    <td class="px-6 py-4">john@example.com</td>
                    <td class="px-6 py-4">Admin</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button class="bg-yellow-400 text-white px-3 py-1 rounded">Edit</button>
                        <button class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                    </td>
                </tr>
                {{-- Repeat with @foreach --}}
            </tbody>
        </table>
    </div>
</div>
@endsection
