<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'DocuTrack PNJ - Dashboard' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body class="font-poppins bg-gradient-to-br from-gray-100 to-teal-100 min-h-screen">
    <div class="main-wrapper font-poppins">
        
        <!-- Include Dynamic Topbar -->
        @include('layouts.partials.topbar')

        <!-- Main Content -->
        @yield('content')
        
    </div>

    @stack('scripts')
</body>
</html>
