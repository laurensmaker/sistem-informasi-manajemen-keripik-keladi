<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <meta name="description" content="Sistem Informasi Manajemen Keripik Keladi">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/sweetalert.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    @stack('styles')

    <link rel="icon" type="image/png" href="{{ asset('backend/assets/images/LOGO-KERIPIK-KELADI.jpg') }}">

    <title>Sistem Informasi Manajemen Keripik Keladi</title>
</head>

<body>

    @include('layouts.sidebar')

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            @include('layouts.header')

            @yield('content')
   
            @include('layouts.footer')
        </div>
    </div>
   
    <!-- ============================================ -->
    <!-- JQUERY (WAJIB UNTUK BANYAK PLUGIN) -->
    <!-- ============================================ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ============================================ -->
    <!-- BOOTSTRAP JS -->
    <!-- ============================================ -->
    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- ============================================ -->
    <!-- PLUGIN JS -->
    <!-- ============================================ -->
    <script src="{{ asset('backend/assets/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('backend/assets/js/dragdrop.js') }}"></script>
    <script src="{{ asset('backend/assets/js/rangeslider.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/sweetalert.js') }}"></script>
    <script src="{{ asset('backend/assets/js/quill.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/data-table.js') }}"></script>
    <script src="{{ asset('backend/assets/js/prism.js') }}"></script>
    <script src="{{ asset('backend/assets/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/amcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/js/custom/custom.js') }}"></script>

    <!-- ============================================ -->
    <!-- INIT FEATHER ICONS -->
    <!-- ============================================ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

    <!-- ============================================ -->
    <!-- STACK SCRIPTS (PENTING UNTUK VIEW) -->
    <!-- ============================================ -->
    @stack('scripts')

</body>

</html>