@extends('admin.layouts.index')
<style>
    /* Dashboard Styles */
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-gold {
        background: linear-gradient(135deg, #FFD700, #FFA500);
    }

    .bg-navy {
        background: #001b4c;
    }

    #userTable {
        border-collapse: collapse;
        width: 100%;
        font-family: 'Inter', sans-serif;
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    #userTable th,
    #userTable td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    #userTable thead th {
        background-color: #f3f4f6;
        font-weight: 600;
        color: #111827;
        position: sticky;
        top: 0;
    }

    #userTable tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    #userTable tbody tr:hover {
        background-color: #f3f4f6;
    }

    .dt-search input[type="search"],
    .dt-length select {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        outline: none;
        margin-bottom: 1rem;
    }

    .dt-search input[type="search"]:focus,
    .dt-length select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
    }

    .top-performer-card {
        transition: all 0.3s ease;
    }

    .top-performer-card:hover {
        transform: scale(1.05);
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
    }

    .loading-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .spinner {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3b82f6;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .chart-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .pagination .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    .pagination .page-link {
        color: #3b82f6;
    }

    .daterangepicker {
        z-index: 9999 !important;
    }

    span.select2-selection.select2-selection--single {
        height: 37px;
        margin-top: 6px;
    }
</style>
@section('content')

    @if (is_role() != 'super_admin')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">

            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3l8.485 4.243a2 2 0 011.07 1.758V12a9 9 0 11-18 0V9.001a2 2 0 011.07-1.758L12 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                    </svg>

                    User Permissions
                </h3>

                <div class="space-y-3">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <h5 class="text-sm font-semibold text-gray-700">Non Script Permissions</h5>
                        <p class="text-2xl font-bold text-blue-600 text-center">{{ $totalPermissions }}</p>
                    </div>

                    <div class="bg-green-50 p-3 rounded-lg">
                        <h5 class="text-sm font-semibold text-gray-700">Script Permissions</h5>
                        <div class="flex flex-wrap gap-2 justify-center mt-2">
                            @foreach ($scriptPermissions as $sp)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium shadow-sm">
                                    {{ $sp }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 18.75a8.987 8.987 0 01-6.364-2.636L3 21l4.886-2.636A8.987 8.987 0 0112 18.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0zM15 9.75l-6 4.5" />
                    </svg>
                    Total Announcements
                </h3>

                <p class="text-3xl font-bold text-purple-600 text-center mb-4">
                    {{ $totalannouncements }}
                </p>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Latest Announcements</h5>
                    <ul class="space-y-2 list-none list-inside text-gray-700">
                        @forelse ($announcements as $sp)
                            <li class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium shadow-sm">
                                {{ $sp->title }}</li>
                        @empty
                            <li class="text-sm text-gray-500 italic">No announcements found</li>
                        @endforelse
                    </ul>
                </div>
            </div>




            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 6.75c0 7.594 6.406 13.75 14.25 13.75h2.25a1.5 1.5 0 001.5-1.5v-2.25a1.5 1.5 0 00-1.5-1.5h-2.25a12 12 0 01-3.75-.625 12 12 0 01-3.75-2.25A12 12 0 017.5 9.75V7.5a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v-.75z" />
                    </svg>
                    Total Contact Button
                </h3>

                <p class="text-3xl font-bold text-purple-600 text-center mb-4">
                    {{ $totalContactbutton }}
                </p>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Latest Contact Button</h5>
                    <ul class="space-y-2 list-none list-inside text-gray-700">
                        @forelse ($Contactbutton as $sp)
                            <li class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium shadow-sm">
                                {{ $sp->title }}</li>
                        @empty
                            <li class="text-sm text-gray-500 italic">No Contact Button found</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

    @endif

    @if (is_role() == 'super_admin')

       <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
         <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 18.75a8.987 8.987 0 01-6.364-2.636L3 21l4.886-2.636A8.987 8.987 0 0112 18.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0zM15 9.75l-6 4.5" />
                    </svg>
                    Total Active User
                </h3>

                <p class="text-3xl font-bold text-purple-600 text-center mb-4">
                    {{ $activeuser }}
                </p>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Latest Active User</h5>
                    <ul class="space-y-2 list-none list-inside text-gray-700">
                        @forelse ($users as $sp)
                            <li class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium shadow-sm">
                                {{ $sp->name }}</li>
                        @empty
                            <li class="text-sm text-gray-500 italic">No Active User found</li>
                        @endforelse
                    </ul>
                </div>
            </div>
<div></div>
<div></div>
       </div>
           
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">

@endpush