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
                                    class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Users</a>
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
                            <th class="p-2">location_id</th>
                            <th class="p-2">Message</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')

<script>
$(document).ready(function () {
    let table = $('#searchtable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.logs.index') }}",
            type: 'GET',
            error: function (xhr) {
                console.error("AJAX Load Error: ", xhr.responseText);
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'location_id', name: 'location_id' },
            { data: 'message', name: 'message' },
        ]
    });

    // Trigger reload
    $('body').on('change', function () {
        table.ajax.reload();
    });
});

</script>
@endpush
