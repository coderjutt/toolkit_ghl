<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Get started with a free and open-source admin dashboard layout built with Tailwind CSS and Flowbite featuring charts, widgets, CRUD layouts, authentication pages, and more">
    <meta name="author" content="Themesberg">
    <meta name="generator" content="Hugo 0.58.2">

    <title>{{ env('APP_NAME') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
    <link rel="stylesheet" href="{{asset('/css/dataTables.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{asset('/css/dataTables.css')}}">
    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/images/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/images/favicon-16x16.png')}}">
    <link rel="icon" type="image/png" href="{{asset('images/favicon.ico')}}">
    <!-- Theme -->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- Scripts -->
    {{-- <script src="{{asset('/js/jquery-3.7.1.min.js')}}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{asset('/css/flowbite.min.css')}}" rel="stylesheet" />
    <script src="{{asset('/js/flowbite.min.js')}}"></script>
    <script src="{{asset('/js/dataTables.js')}}"></script>
    <script src="{{asset('/js/tailwindcss.js')}}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#001b4c',
                        gold: '#b28c35',
                        'gold-light': '#d4b15e',
                        'navy-light': '#0a2a66',
                        'navy-dark': '#001233',
                    }
                }
            }
        }
    </script>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- Custom Styles -->
    <style>

 table#searchtable {
    width: 1200px !important;
}
        /* Navy Blue & Gold Color Scheme */
        .navy-bg {
            background-color: #001b4c;
        }

        .gold-bg {
            background-color: #b28c35;
        }

        .navy-text {
            color: #001b4c;
        }

        .gold-text {
            color: #b28c35;
        }

        .gold-border {
            border-color: #b28c35;
        }

        .navy-border {
            border-color: #001b4c;
        }

        .gold-hover:hover {
            background-color: #9c7a2d;
        }

        .navy-hover:hover {
            background-color: #0a2a66;
        }

        /* Sidebar */
        #sidebar {
            background-color: #001b4c;
        }

        #sidebar a {
            color: #ffffff;
        }

        #sidebar a:hover, #sidebar a.active {
            background-color: rgba(178, 140, 53, 0.15);
        }

        #sidebar a:hover i, #sidebar a.active i {
            color: #b28c35;
        }

        /* Header */
        nav {
            background-color: #001b4c;
            color: white;
        }

        /* Buttons */
        .btn-gold {
            background-color: #b28c35;
            color: white;
        }

        .btn-gold:hover {
            background-color: #9c7a2d;
        }

        /* Stats Cards */
        .stat-card {
            background-color: white;
            border: 1px solid #e5e7eb;
        }

        .stat-card h3 {
            color: #6b7280;
        }

        .stat-card p {
            color: #001b4c;
            font-weight: bold;
        }

        /* Progress Bars */
        .progress-bar {
            background-color: #e5e7eb;
        }

        .progress-fill {
            background-color: #b28c35;
        }

        /* Tables */
        thead tr th {
            background: #f3f4f6 !important;
            color: #001b4c;
        }

        tbody tr td {
            background: #ffffff !important;
        }

        /* Top Performers */
        .top-performer .badge {
            background-color: #b28c35;
            color: white;
        }

        /* Charts */
        #donut-chart {
            background-color: white;
        }

        /* General styles */
        thead tr th {
            background: #ffffff !important;
        }

        tbody tr td {
            background: #ffffff !important;
            box-shadow: transparent !important;
        }

        table.dataTable.order-column > tbody tr > .sorting_1,
        table.dataTable.order-column > tbody tr > .sorting_2,
        table.dataTable.order-column > tbody tr > .sorting_3,
        table.dataTable.display > tbody tr > .sorting_1,
        table.dataTable.display > tbody tr > .sorting_2,
        table.dataTable.display > tbody tr > .sorting_3,
        table.dataTable.display > tbody > tr:nth-child(odd) > .sorting_1,
        table.dataTable.order-column.stripe > tbody > tr:nth-child(odd) > .sorting_1 {
            box-shadow: none !important;
        }

        table.dataTable.stripe > tbody > tr:nth-child(odd) > *,
        table.dataTable.display > tbody > tr:nth-child(odd) > * {
            box-shadow: none !important;
        }

        span.select2-selection.select2-selection--single {
            height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 28px;
            justify-content: center;
            text-align: start;
            padding-top: 3px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px;
            position: absolute;
            top: 5px;
            right: 1px;
            width: 20px;
        }

    </style>

    @stack('scripts')
</head>
