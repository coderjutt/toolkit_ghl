@extends('admin.layouts.index')

@section('content')
    <div class="flex justify-center items-center min-h-[50vh] bg-gray-100">
        <div class="bg-white shadow-xl rounded-2xl w-full max-w-md">

            <!-- Card Header -->
            <div class="bg-blue-600 text-white text-center py-4 rounded-t-2xl">
                <h4 class="text-lg font-semibold">✏️ Edit Custom Value</h4>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <form method="POST" action="{{ route('admin.api.custom-values.update', $value->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Company Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                        <div class="flex items-center border rounded-lg overflow-hidden">
                            <span class="px-3 bg-gray-100 text-gray-500">
                                <i class="fa fa-envelope"></i>
                            </span>
                            <input type="email" name="company_email"
                                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('company_email', $value->company_email) }}" placeholder="Enter company email"
                                required>
                        </div>
                        @error('company_email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agent Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Agent Name</label>
                        <div class="flex items-center border rounded-lg overflow-hidden">
                            <span class="px-3 bg-gray-100 text-gray-500">
                                <i class="fa fa-user"></i>
                            </span>
                            <input type="text" name="agent_name"
                                class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('agent_name', $value->agent_name) }}" placeholder="Enter agent name" required>
                        </div>
                        @error('agent_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('admin.api.custom-values.index') }}"
                            class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fa fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection