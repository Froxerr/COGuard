<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CO Guard - @yield('title')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --text-light: #95a5a6;
            --border-color: rgba(0,0,0,0.05);
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            color: var(--text-primary);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--text-primary);
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--secondary-color), var(--dark-color));
            box-shadow: var(--shadow-md);
            position: fixed;
            width: 280px;
            z-index: 1000;
            padding: 0 15px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 8px 0;
            transition: var(--transition);
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .sidebar .nav-link i {
            width: 30px;
            text-align: center;
            margin-right: 15px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 280px);
        }

        .navbar {
            background-color: white;
            box-shadow: var(--shadow-sm);
            padding: 1rem 2rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            font-family: 'Poppins', sans-serif;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: var(--shadow-sm);
            font-size: 1.2rem;
            font-family: 'Poppins', sans-serif;
        }

        .content-wrapper {
            flex: 1;
            padding: 2.5rem;
            max-width: 100%;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-body {
            padding: 2rem;
        }

        .footer {
            background-color: white;
            padding: 1.5rem 2rem;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            margin-top: auto;
        }

        .status-badge {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: rgba(46, 204, 113, 0.15);
            color: var(--success-color);
        }

        .status-inactive {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--danger-color);
        }

        .status-warning {
            background-color: rgba(241, 196, 15, 0.15);
            color: var(--warning-color);
        }

        .metric-card {
            text-align: center;
            padding: 2rem;
        }

        .metric-value {
            font-size: 2.8rem;
            font-weight: 700;
            margin: 1.2rem 0;
            color: var(--primary-color);
            font-family: 'Poppins', sans-serif;
        }

        .metric-label {
            font-size: 1rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 500;
        }

        .metric-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.2rem;
        }

        .sidebar-title {
            padding: 1.5rem 0;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-title h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .logout-btn {
            color: white;
            background-color: rgba(231, 76, 60, 0.2);
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background-color: rgba(231, 76, 60, 0.4);
            color: white;
        }

        .table {
            font-size: 0.95rem;
        }

        .table th {
            font-weight: 600;
            color: var(--text-primary);
            border-top: none;
            padding: 1rem 1.5rem;
        }

        .table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            color: var(--text-secondary);
        }

        .btn {
            font-weight: 500;
            padding: 0.5rem 1.2rem;
            border-radius: 6px;
            transition: var(--transition);
        }

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .d-flex.align-items-center h6 {
            margin-bottom: 0.3rem;
            font-size: 0.95rem;
        }

        .d-flex.align-items-center i {
            margin-right: 0.5rem;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
                padding: 0 10px;
            }

            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
                width: 100%;
                text-align: center;
            }

            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .sidebar-title h4 {
                display: none;
            }

            .content-wrapper {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-title text-center">
                <a href="/" class="text-decoration-none">
                    <h4>CO Guard <i class="fa fa-heartbeat"></i></h4>
                </a>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('system-status') ? 'active' : '' }}" href="{{ route('system-status') }}">
                    <i class="fas fa-chart-line"></i> <span>Sistem Durumu</span>
                </a>
                <a class="nav-link {{ request()->routeIs('panel.alarm-control') ? 'active' : '' }}" href="{{ route('panel.alarm-control') }}">
                    <i class="fas fa-bell"></i> <span>Alarm Kontrolü</span>
                </a>
                <a class="nav-link {{ request()->routeIs('panel.alarm-limits-control') ? 'active' : '' }}" href="{{ route('panel.alarm-limits-control') }}">
                    <i class="fas fa-sliders-h"></i> <span>Eşik Değerleri</span>
                </a>
                <a class="nav-link {{ request()->routeIs('panel.sensors-settings') ? 'active' : '' }}" href="{{ route('panel.sensors-settings') }}">
                    <i class="fas fa-cog"></i> <span>Sensör Ayarları</span>
                </a>
                <a class="nav-link {{ request()->routeIs('panel.alarms-history') ? 'active' : '' }}" href="{{ route('panel.alarms-history') }}">
                    <i class="fas fa-history"></i> <span>Alarm Geçmişi</span>
                </a>
                <a class="nav-link {{ request()->routeIs('panel.measurements-history') ? 'active' : '' }}" href="{{ route('panel.measurements-history') }}">
                    <i class="fas fa-chart-bar"></i> <span>Sensör Ölçümleri</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item d-flex align-items-center">
                                <div class="user-profile me-3">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span>{{ auth()->user()->name ?? 'Kullanıcı' }}</span>
                                </div>
                                <a href="{{ route('logout') }}" class="logout-btn">
                                    <i class="fas fa-sign-out-alt"></i> Çıkış
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="content-wrapper">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 text-md-start">
                            <p class="mb-0">&copy; {{ date('Y') }} CO Guard. Tüm hakları saklıdır.</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0">Versiyon 1.0.0 | <a href="#" class="text-decoration-none">Yardım</a></p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Firebase JavaScript SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>

    <!-- Firebase Yapılandırması -->
    <script>
    // Firebase yapılandırması - tek bir yerden yönetim için burada tanımlandı
    const firebaseConfig = {
        
        };

    // Firebase'i başlat (eğer henüz başlatılmadıysa)
    if (!window.firebase || !firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }

    // Global erişim için database nesnesini tanımla
    const database = firebase.database();
    </script>

    @stack('scripts')
</body>
</html>
