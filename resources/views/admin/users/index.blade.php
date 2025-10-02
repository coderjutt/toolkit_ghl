@extends('admin.layouts.index')

@push('styles')
    <!-- DataTables CSS -->
    <style>
      /* Adjust DataTables elements to fit Tailwind styling */

      /* Align the search input & length select */
      .dataTables_wrapper .dataTables_filter input,
      .dataTables_wrapper .dataTables_length select {
        @apply border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500;
      }

      /* Style pagination buttons */
      .dataTables_wrapper .dataTables_paginate .paginate_button {
        @apply mx-1 px-3 py-1 border border-gray-300 rounded text-gray-700;
      }
      .dataTables_wrapper .dataTables_paginate .paginate_button.current,
      .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        @apply bg-blue-600 text-white border-blue-600;
      }

      /* Table header */
      #searchtable thead th {
        @apply bg-gray-100 text-gray-800 font-medium;
      }
      /* Table rows hover */
      #searchtable tbody tr:hover {
        @apply bg-gray-50;
      }
      /* Ensure responsive overflow */
      .dataTables_wrapper .dataTables_scroll {
        overflow-x: auto;
      }
    </style>
@endpush

@section('content')
   <div class="grid grid-cols-12 gap-6 mb-8">
     <!-- Breadcrumb Card -->
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
               <span class="ml-1 text-gray-400 md:ml-2" aria-current="page">List</span>
             </div>
           </li>
         </ol>
       </nav>
     </div>

     <!-- Table Card -->
     <div class="col-span-12 overflow-x-auto bg-white rounded-xl shadow-sm p-5 overflow-x-auto">
       <table id="searchtable" class="display w-full text-sm text-left">
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

     function saveData(id, name,manual_key, email, location_id) {
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
    $('#manual_key').val(manual_key);
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
