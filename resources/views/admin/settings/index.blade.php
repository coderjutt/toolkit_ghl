@extends('admin.layouts.index')
@section('content')
    <style>
        .loader {
            --d: 22px;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            color: #25b09b;
            box-shadow:
                calc(1*var(--d)) calc(0*var(--d)) 0 0,
                calc(0.707*var(--d)) calc(0.707*var(--d)) 0 1px,
                calc(0*var(--d)) calc(1*var(--d)) 0 2px,
                calc(-0.707*var(--d)) calc(0.707*var(--d)) 0 3px,
                calc(-1*var(--d)) calc(0*var(--d)) 0 4px,
                calc(-0.707*var(--d)) calc(-0.707*var(--d))0 5px,
                calc(0*var(--d)) calc(-1*var(--d)) 0 6px;
            animation: l27 1s infinite steps(8);
        }

        @keyframes l27 {
            100% {
                transform: rotate(1turn);
            }
        }
    </style>
    <div class="px-4 pt-6 ">


        <!-- Loader -->
        <div id="custom-loader"
            style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 9999; align-items: center; justify-content: center;">
            <div class="loader"></div>
        </div>

        <div
            class="grid grid-cols-12 gap-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">

            @if (is_role() == 'super_admin')
                <div class="col-span-12 xl:col-span-6 xl:col-start-4">
                    <div
                        class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <h3 class="mb-4 text-xl font-semibold dark:text-white"> CRM OAuth Information</h3>
                        <form method="POST" action="{{ route('admin.setting.save') }}">
                            @csrf
                            <div class="mb-4">

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="first-name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client
                                        ID</label>
                                    <input type="text" value="{{ $settings['crm_client_id'] ?? '' }}" id="crm_client_id"
                                        name="setting[crm_client_id]"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Client ID" required>
                                </div>
                            </div>

                            <div class="mb-6">

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="first-name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client
                                        secret</label>
                                    <input type="text" value="{{ $settings['crm_client_secret'] ?? '' }}"
                                        id="crm_secret_id" name="setting[crm_client_secret]"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Client secret" required>
                                </div>
                            </div>

                            <div>
                                <button type="submit"
                                    class="btn btn-primary text-white bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
             @if (is_role() == 'admin')
            <div class="col-span-12 sm:col-span-10 md:col-span-8 xl:col-span-6 xl:col-start-4">
                <div
                    class="flex flex-col sm:flex-row justify-between gap-4 w-full p-6 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                        <a href="{{ CRM::directConnect() }}"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Connect {{ is_role() == 'super_admin' ? 'Agency' : 'Location' }}/CRM
                        </a>
                    <button id="sync-location-data-btn"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Sync Location Data
                    </button>
                </div>
            </div>
              @endif

        </div>
 @if (is_role() == 'admin')
        <div
            class="grid grid-cols-12 mt-4 gap-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">

                <div class="col-span-12 xl:col-span-6 xl:col-start-4">
                    <div
                        class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <h3 class="mb-4 text-xl font-semibold dark:text-white">Goals Per Agent</h3>


                        <form method="POST" action="{{ route('admin.subaccount.policies') }}">
                            @csrf
                            <div class="mb-4">

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="first-name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Policies</label>
                                    <input type="text" value="{{ $policies['policies'] ?? ''}}" name="policies[policies]"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Policies" required>
                                </div>
                            </div>

                            <div class="mb-6">

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="first-name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Premium Goal</label>
                                    <input type="text"  value="{{$policies['premium']  ?? ''}}" name="policies[premium]"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Premium" required>
                                </div>
                            </div>

                            <div>
                                <button type="submit"
                                    class="btn btn-primary text-white bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

        </div>
        @endif
        @php
            $logo = App\Models\Setting::where('key', 'logo')->first();
        @endphp
        <div
            class="gap-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 mt-4">
            <div class="flex items-center justify-center w-full mb-4">
            </div>
            <div class="flex items-center justify-center w-full">
                <label id="dropzone" for="dropzone-file"
                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        @if ($logo)
                            <img src="{{ asset('logo/' . $logo->value) }}" alt="Current Logo"
                                class="max-h-32 object-contain rounded-lg shadow-md">
                        @else
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                        @endif
                    </div>
                  <input id="dropzone-file" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg, image/gif, image/svg+xml" />

                </label>
            </div>

        </div>

    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script>
        $(document).ready(function() {
            $('#sync-location-data-btn').click(function() {
                $('#custom-loader').css('display', 'flex'); // SHOW loader

                $.ajax({
                    url: "{{ route('crm.syn.location.data') }}",
                    type: 'GET',
                    success: function(response) {
                        $('#custom-loader').css('display', 'flex'); // SHOW loader
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        alert('Something went wrong. Please try again.');
                        console.log(xhr.responseText);
                    },
                    complete: function() {
                        $('#custom-loader').hide(); // HIDE loader
                    }
                });
            });
        });

      document.addEventListener("DOMContentLoaded", function () {
        const dropzone = document.getElementById("dropzone");
        const inputFile = document.getElementById("dropzone-file");

        dropzone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropzone.classList.add("bg-gray-200");
        });

        dropzone.addEventListener("dragleave", () => {
            dropzone.classList.remove("bg-gray-200");
        });

        dropzone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropzone.classList.remove("bg-gray-200");

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadImage(files[0]);
            }
        });

        inputFile.addEventListener("change", function () {
            if (this.files.length > 0) {
                uploadImage(this.files[0]);
            }
        });

        function uploadImage(file) {
            const formData = new FormData();
            formData.append("image", file);

            fetch("{{ route('admin.setting.saveLogo') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Upload failed');
                return response.json();
            })
            .then(data => {
                alert("Upload successful");
                location.reload();
            })
            .catch(error => {
                alert("Upload failed");
                console.error(error);
            });
        }

        // Optional: form submission override (if needed)
        const form = document.querySelector("form[action='{{ route('admin.setting.save') }}']");
        if (form) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                fetch(form.action, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert("Settings saved!");
                    console.log(data);
                })
                .catch(error => {
                    alert("Failed to save settings.");
                    console.error(error);
                });
            });
        }
    });
    </script>
@endpush
