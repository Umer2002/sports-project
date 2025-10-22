<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $club->name ?? 'Club' }} - {{ $club->sport->name ?? 'Sports Club' }}</title>
  
  <!-- Base CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <!-- Theme-specific CSS -->
  @yield('theme-css')
  
  <!-- Custom styles -->
  <style>
    .menu-bar{
      padding-bottom: 0px;
      padding-top: 0px;
    }
    .stats-value{
      font-size: 38px;
    }
  </style>
</head>

<body>
  <main class="main">
    <div class="d-flex">
      <!-- Sidebar (Aside) start -->
      @yield('sidebar')
      <!-- Sidebar (Aside) end -->
      
      <div style="width: 100%;">
        <!-- header start  -->
        @yield('header')
        <!-- header end -->
        
        <!-- hero section start -->
        @yield('hero')
        <!-- hero section end -->
      </div>
    </div>
  </main>
  
  <!-- Tab Content -->
  @yield('tabs')
  
  <!-- Newsletter Section -->
  @yield('newsletter')
  
  <!-- Footer -->
  @yield('footer')
  
  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Theme-specific scripts -->
  @yield('theme-scripts')
</body>

</html>
