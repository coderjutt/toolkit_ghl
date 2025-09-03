@extends('admin.layouts.index')

@push('styles')
    <style>
        table.dataTable th.dt-type-numeric,
        table.dataTable th.dt-type-date,
        table.dataTable td.dt-type-numeric,
        table.dataTable td.dt-type-date Specificity: (0, 2, 2) {
            text-align: left !important;
        }

        select#dt-length-0 {
            width: 100px !important;
        }
    </style>
@endpush

@section('content')
    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="#"
                                class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                    </path>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <a href="#"
                                    class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">User</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">List</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="sm:flex">
                {{-- <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                    <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                        class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        Add SubAccount
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="grid gap-4 xl:grid-cols-2 2xl:grid-cols-3">
        <!-- Adjust the table to take up 10 columns and apply an offset of 1 column on both sides -->
        <div class="col-span-1 xl:col-span-2 2xl:col-span-3 flex flex-col ml-4 mr-4 py-10">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table id="searchtable" class="display w-full text-sm text-left table-auto">
                            <thead>
                                <tr>
                                    <th class="p-2" width="10%">ID</th>
                                    <th class="p-2">Name</th>
                                    <th class="p-2">Email</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2" width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="authentication-modal" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-lg">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                     <h3 class="text-xl font-semibold dark:text-white" id="form_header">
                        Add New SubAccount
                    </h3>
                    <button type="button"  onclick="closeAllModals()"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="authentication-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form id="userForm">
                        @csrf
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 ">
                                    <label for="first-name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                    <input type="hidden" name="id" id="user_id" class=""
                                        placeholder="Please Enter First Name" required="">
                                    <input type="text" name="name" id="name"
                                        class="shadow-sm  border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Please Enter First Name" required="">
                                </div>

                                <div class="col-span-6 ">
                                    <label for="email"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                    <input type="email" name="email" id="email"
                                        class="shadow-sm  border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Please Enter Email" required="">
                                </div>
                                <div class="col-span-6 ">
                                    <label for="position"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                    <input type="password" name="password" id="password"
                                        class="shadow-sm  border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Please Enter Password" required="">
                                </div>
                                <div class="col-span-6 ">
                                    <label for="position"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location
                                        ID</label>
                                    <input type="text" name="location_id" id="location_id"
                                        class="shadow-sm  border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Please Enter Password" required="">
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="items-center p-6 border-t border-gray-200 rounded-b dark:border-gray-700">
                            <button
                                class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                id="submitButton" type="submit">Add SubAccount</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#searchtable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.user.index') }}",
                    type: 'GET',
                    error: function(xhr) {
                        console.error("AJAX Load Error: ", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Trigger reload
            $('body').on('change', function() {
                table.ajax.reload();
            });

            // Form submission
            // Form submission
            $("form#userForm").submit(function(event) {
                event.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('admin.user.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Hide the correct modal
                        $('#authentication-modal').addClass('hidden');

                        // Optionally reload table if used
                        if (typeof table !== 'undefined') {
                            table.ajax.reload(null, false);
                        }

                        // Remove modal overlay if necessary
                        $('.bg-gray-900\\/50, .bg-gray-900\\/80').remove();
                    },
                    error: function(error) {
                        console.error("Form Submit Error: ", error.responseText);
                    }
                });
            });
            // Delete
            $('body').on('click', '.confirm-delete', function(e) {
                e.preventDefault();
                const userId = $(this).data('id');
                const url = '{{ route('admin.user.destroy', ':id') }}'.replace(':id', userId);

                if (confirm("Are you sure you want to delete this user?")) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.error("Delete Error: ", xhr.responseText);
                        }
                    });
                }
            });

            // Status change
            $('body').on('click', '.status_changes', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                console.log("Status change triggered for user ID:", id);

                const url = "{{ route('admin.user.status', ':id') }}".replace(':id', id);

                if (confirm("Are you sure you want to change the status of this user?")) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            console.log("Status updated successfully");
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.error("Status Change Error:", xhr.responseText);
                        }
                    });
                }
            });
             $('body').on('click', '.editModelOpen', function(e) {
                 $('#authentication-modal').removeClass('hidden');
             });
        });

     function saveData(id, name, email, location_id) {
    const modal = document.getElementById('authentication-modal');
    const modalContent = modal.querySelector('.relative.p-4.w-full.max-w-lg');
    if (modalContent) {
        modalContent.classList.add('mx-auto');
    }
    const formHeader = document.getElementById('form_header');
    if (formHeader) {
        formHeader.textContent = 'Edit Practice';
    }
    $('#user_id').val(id);
    $('#name').val(name);
    $('#email').val(email);
    $('#password').val('');
    $('#location_id').val(location_id);
    modal.classList.remove('hidden');
    $('body').append('<div class="modal-backdrop fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 z-40"></div>');
     $('body').append('<div class="modal-backdrop fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 z-40"></div>');
    $('body').css('overflow', 'hidden');
}


function closeAllModals() {
    $('[id$="-modal"]').addClass('hidden');

    // Remove all modal backdrops
    $('.modal-backdrop').remove();

    $('body').css('overflow', 'auto');
}
    </script>
@endpush
