@extends('admin.layouts.index')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">CustomMenuLink</h1>
            <!-- Add Location Button -->
            <button onclick="document.getElementById('locationModal').classList.remove('hidden')"
                class="bg-blue-600 text-white px-4 py-2 rounded">
                CustomMenuLink
            </button>
        </div>

        {{-- Location Table --}}
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                <thead class="bg-gray-100 text-left text-xs uppercase font-semibold text-gray-600">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Allowed_Emails</th>
                        <th class="px-6 py-3 text-center">Restricted_Email</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                   <td class="px-6 py-4">test</td>
                </tbody>
            </table>
        </div>
    </div>
@endsection