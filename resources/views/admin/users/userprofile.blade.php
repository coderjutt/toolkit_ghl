@extends('admin.layouts.index')

<style>

      
    </style>

@section('content')
    <div class="px-4 pt-24 pb-10 bg-gray-100 dark:bg-gray-900">
        <!-- Breadcrumb and Header -->
        <div class="col-span-12 bg-white rounded-xl shadow-sm p-5">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center text-gray-700 hover:text-primary-600">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0
                                    001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0
                                    001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0
                                        011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0
                                        01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <a href="#" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2">
                                User
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0
                                        011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0
                                        01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-400 md:ml-2" aria-current="page">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <br>
        <!-- Settings Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Profile Picture Upload -->
            <div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800 dark:border dark:border-gray-700">
                <div class="flex items-center space-x-6">
                    <img src="{{ asset(Auth::user()->image) }}" alt="User Image"
                        class="w-24 h-24 rounded-full border-2 border-primary-600 object-cover">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Picture</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">JPG, PNG or GIF. Max 800KB.</p>
                        <button type="button"
                            class="mt-2 inline-flex items-center px-3 py-1.5  text-sm rounded btn-primary-custom upload-picture-btn">
                            Upload Picture
                        </button>
                    </div>
                </div>

                <div class="toggle mt-4 hidden">
                    <form action="{{ route('admin.user.imageUpload') }}" method="POST" enctype="multipart/form-data"
                        class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-4">
                        @csrf
                        <input type="file" name="image"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600">
                        <button type="submit"
                            class="px-4 py-2 rounded btn-primary-custom upload-picture-btn">
                            Submit
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800 dark:border dark:border-gray-700 xl:col-span-1">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Change Password</h2>
                <form action="{{ route('admin.user.password.save') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="current-password"
                            class="block text-sm font-medium text-gray-700 dark:text-white">New Password</label>
                        <input type="password" name="password" id="current-password" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="confirm_password"
                            class="block text-sm font-medium text-gray-700 dark:text-white">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <button type="submit"
                        class="w-full font-medium py-2 rounded btn-primary-custom">
                        Update Password
                    </button>
                </form>
            </div>

            <!-- General Information -->
            <div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800 dark:border dark:border-gray-700 xl:col-span-1">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">General Information</h2>
                <form action="{{ route('admin.user.profile.save') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="first-name"
                            class="block text-sm font-medium text-gray-700 dark:text-white">First Name</label>
                        <input type="text" name="first-name" id="first-name"
                            value="{{ old('name', $user->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-white">Email</label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email', $user->email) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <button type="submit"
                        class="w-full font-medium py-2 rounded btn-primary-custom">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.upload-picture-btn').on('click', function () {
                $('.toggle').toggle();
            });
        });
    </script>
@endpush
