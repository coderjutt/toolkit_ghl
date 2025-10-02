<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ env('APP_NAME', 'Xortlogix Toolkit') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/dataTables.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{ asset('/css/dataTables.css') }}">
    <script src="{{ asset('/js/jquery-3.7.1.min.js') }}"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="{{ asset('/css/flowbite.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('/js/flowbite.min.js') }}"></script>
    <script src="{{ asset('/js/dataTables.js') }}"></script>
    <script src="{{ asset('/js/tailwindcss.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#001b4c',
                        gold: '#b28c35',
                        'gold-light': '#d4b15e',
                        'navy-light': '#0a2a66',
                        'navy-dark': '#001233'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        [x-cloak] {
            display: none !important;
        }

        .progress-bar-background-color {
            background: rgb(132 134 139);
            ;
        }

        .nav-links a.active {
            background: rgba(178, 140, 53, 0.15);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .chart-placeholder {
            background: #f5f7fa;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 20;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 30;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        /* Smooth sidebar transitions */
        .sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Better scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .btn-primary-custom {
            background-color: #001b4c !important;
            color: white !important;
        }

        .btn-primary-custom:hover {
            background-color: #001437 !important;
        }

        .btn-primary-custom:focus {
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(0, 27, 76, 0.4) !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #aaa;
            border-radius: 3px;
            padding: 5px;
            background-color: transparent;
            padding: 4px 20px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px;
            position: absolute;
            top: 11px;
            right: 1px;
            width: 20px;
            
        }
        
         .loader {
            width: 64px;
            height: 48px;
            position: relative;
            animation: split 1s ease-in infinite alternate;
        }

        .loader::before,
        .loader::after {
            content: '';
            position: absolute;
            height: 48px;
            width: 48px;
            border-radius: 50%;
            left: 0;
            top: 0;
            transform: translateX(-10px);
            background: #FF3D00;
            opacity: 0.75;
            backdrop-filter: blur(20px);
        }

        .loader::after {
            left: auto;
            right: 0;
            background: #FFF;
            transform: translateX(10px);
        }

        @keyframes split {

            0%,
            25% {
                width: 64px
            }

            100% {
                width: 148px
            }
        }
    </style>
</head>

<body class="bg-gray-50 flex min-h-screen">
     <div id="pageLoader" class="fixed inset-0 flex items-center justify-center bg-white z-[9999]">
                <div class="loader"></div>
            </div>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    @include('admin.layouts.sidebar')
    <!-- Main Content -->
    <div class="main-content w-full flex-1 p-4 md:p-6 mt-16">
        <!-- Header -->
        @include('admin.layouts.header')
        <!-- Stats Cards -->
        <div class="dashboard-stats gap-5 mb-8">

            @yield('content')
            <!-- Footer -->
            <footer class=" fixed bottom-0 text-center mt-8 pt-5 w-full text-center py-3 shadow-md z-50 border-t border-gray-200">
                <p class="text-gray-600">&copy; 2025{{ env('APP_NAME', 'Xortlogix Toolkit') }} . All rights reserved.
                </p>
            </footer>
            
        </div>
        @stack('scripts')
        <script src="{{ asset('/js/app.bundle.js') }}"></script>
        <script src="{{ asset('/js/datepicker.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
        <script src="//unpkg.com/alpinejs" defer></script>
        @yield('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mobileToggle = document.querySelector('.mobile-toggle');
                const navLinks = document.getElementById('nav-links');

                // Toggle mobile menu visibility
                mobileToggle.addEventListener('click', () => {
                    navLinks.classList.toggle('hidden');
                });

                // Optional: Auto-close nav menu when a link is clicked (on mobile)
                const navItems = navLinks.querySelectorAll('a');
                navItems.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth <= 768) {
                            navLinks.classList.add('hidden');
                        }
                    });
                });
            });
        </script>
        
         <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Jab page DOM ready ho loader hata do
                const loader = document.getElementById("pageLoader");
                window.addEventListener("load", function () {
                    loader.style.opacity = "0";
                    setTimeout(() => loader.style.display = "none", 800); // smooth hide
                });
            });
        </script>


</body>

</html>
