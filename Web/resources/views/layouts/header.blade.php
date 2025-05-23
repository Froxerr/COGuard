
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>CO Guard</title>
    <meta name="description" content="CO Guard - Güvenli Yaşam İçin Akıllı Karbon Monoksit İzleme Sistemi">
    <meta name="keywords" content="CO, karbon monoksit, güvenlik, sensör, akıllı ev">

    <!-- Critical CSS -->
    <style>
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: opacity 0.3s;
        }

        .preloader-bar {
            width: 200px;
            height: 3px;
            background-color: #f0f0f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .preloader-bar::after {
            content: '';
            display: block;
            width: 40%;
            height: 100%;
            background-color: #007bff;
            animation: loading 1s infinite ease-in-out;
        }

        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(250%); }
        }

        /* Hide main content until fully loaded */
        main {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .content-visible {
            opacity: 1 !important;
            transition: opacity 0.5s ease-in-out;
        }
    </style>

    <!-- Favicons -->
    <link href="{{asset("assets/img/favicon.png")}}" rel="icon">
    <link href="{{asset("assets/img/apple-touch-icon.png")}}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files - Asenkron yükleme -->
    <link href="{{asset("assets/vendor/bootstrap/css/bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{asset("assets/vendor/bootstrap-icons/bootstrap-icons.css")}}" rel="stylesheet">
    <link href="{{asset("assets/vendor/aos/aos.css")}}" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{asset("assets/vendor/glightbox/css/glightbox.min.css")}}" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{asset("assets/vendor/swiper/swiper-bundle.min.css")}}" rel="stylesheet" media="print" onload="this.media='all'">

    <!-- Main CSS File -->
    <link href="{{asset("assets/css/main.css")}}" rel="stylesheet">
    <link href="{{asset("assets/css/preloader.css")}}" rel="stylesheet">

     <!-- Preloader -->
     <div class="preloader">
        <div class="preloader-bar"></div>
    </div>

</head>

<body class="index-page">

<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="/" class="logo d-flex align-items-center me-auto me-xl-0">
            <img src="{{asset("assets/img/logo.png")}}" alt="">
            <h1 class="sitename">CO Guard</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="/#hero" class="{{ request()->is('/') ? 'active' : '' }}">Anasayfa</a></li>
                <li><a href="/#about">Hakkımızda</a></li>
                <li><a href="/#features">Özellikler</a></li>
                <li><a href="/#mobil">Mobil Uygulama</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
        @if(!(Session::has('user') && Session::get('user')['is_logged_in']))
            <a class="btn-getstarted" href="/register">Hemen Başla</a>
        @endif
    </div>
</header>

<!-- Preloader JS -->
<script>
    // Sayfa yüklenme performansını artırmak için preloader kodunu inline yapıyorum
    document.addEventListener('DOMContentLoaded', function() {
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.style.opacity = '0';
        }
    });

    window.addEventListener('load', function() {
        const preloader = document.querySelector('.preloader');
        const mainContent = document.querySelector('main');

        // Sayfa yüklendikten hemen sonra içeriği göster
        if (mainContent) {
            mainContent.style.opacity = '1';
            mainContent.style.transition = 'opacity 0.5s ease-in-out';
        }
        
        // Preloader'ı kaldır
        if (preloader) {
            preloader.style.opacity = '0';
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 300);
        }
    });
</script>
<!-- <script src="{{asset("assets/js/preloader.js")}}"></script> -->
<script src="{{asset("assets/js/scroll-spy.js")}}"></script>

<!-- Login Success Popup - Bu kısmı kaldırıyorum çünkü footer'da zaten var -->
<!-- @if(Session::has('success'))
<script>
    // Sayfa tamamen yüklendiğinde çalışacak
    document.addEventListener('DOMContentLoaded', function() {
        // Sayfanın tamamen yüklenmesi için kısa bir gecikme
        setTimeout(function() {
            toastr.success("{{ Session::get('success') }}", "Başarılı!");
        }, 500);
    });
</script>
@endif -->
