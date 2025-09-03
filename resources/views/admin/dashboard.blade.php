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

    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">

@endpush
