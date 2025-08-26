<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FABRICASOFT - SENA')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sena-green: #39A900;
            --sena-dark-green: #2E7D32;
            --sena-light-green: #4CAF50;
            --sena-yellow: #FFC107;
            --sena-orange: #FF9800;
            --sena-blue: #2196F3;
            --sena-gray: #607D8B;
            --sena-light-gray: #F5F5F5;
            --sena-dark-gray: #424242;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--sena-light-gray);
            margin: 0;
            padding: 0;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, var(--sena-green) 0%, var(--sena-dark-green) 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-header .logo {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }
        
        .sidebar-header .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin: 0;
        }
        
        .sidebar-header .logo-text.collapsed {
            display: none;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: var(--sena-yellow);
        }
        
        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.15);
            border-left-color: var(--sena-yellow);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
        }
        
        .nav-link .nav-text {
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-nav .toggle-sidebar {
            background: var(--sena-green);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .top-nav .toggle-sidebar:hover {
            background: var(--sena-dark-green);
        }
        
        .top-nav .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .top-nav .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--sena-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .top-nav .user-details h6 {
            margin: 0;
            color: var(--sena-dark-gray);
            font-weight: 600;
        }
        
        .top-nav .user-details small {
            color: var(--sena-gray);
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--sena-green), var(--sena-dark-green));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }
        
        /* Buttons */
        .btn-sena {
            background: var(--sena-green);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-sena:hover {
            background: var(--sena-dark-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(57, 169, 0, 0.3);
        }
        
        .btn-sena-outline {
            background: transparent;
            border: 2px solid var(--sena-green);
            color: var(--sena-green);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-sena-outline:hover {
            background: var(--sena-green);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, var(--sena-green), var(--sena-dark-green));
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .stats-card .stats-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stats-card .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-card .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
        }
        
        /* Welcome Page Specific Styles */
        .welcome-hero {
            background: linear-gradient(135deg, var(--sena-green) 0%, var(--sena-dark-green) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            border-radius: 20px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .welcome-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .welcome-hero .lead {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .feature-card:hover {
            border-color: var(--sena-green);
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(57, 169, 0, 0.15);
        }
        
        .feature-card .feature-icon {
            font-size: 3rem;
            color: var(--sena-green);
            margin-bottom: 20px;
        }
        
        .feature-card h4 {
            color: var(--sena-dark-gray);
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .feature-card p {
            color: var(--sena-gray);
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/8/83/Sena_Colombia_logo.svg" alt="SENA Logo" class="logo">
            <h5 class="logo-text" id="logoText">FABRICASOFT</h5>
        </div>
        
        <nav class="sidebar-nav">
            @auth
                @if(Auth::user()->hasCustomRole('fabricasoft.admin'))
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.admin.inicio') }}" class="nav-link {{ request()->routeIs('fabricasoft.admin.inicio') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">Inicio</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.admin.dashboard.view') }}" class="nav-link {{ request()->routeIs('fabricasoft.admin.dashboard.view') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i>
                            <span class="nav-text">Nuevas Solicitudes</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.admin.projects.list') }}" class="nav-link {{ request()->routeIs('fabricasoft.admin.projects.*') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span class="nav-text">Equipos Scrum</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.admin.solicitudes') }}" class="nav-link {{ request()->routeIs('fabricasoft.admin.solicitudes') ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span class="nav-text">Todas las Solicitudes</span>
                        </a>
                    </div>
                @endif
                
                @if(Auth::user()->hasCustomRole('fabricasoft.analista') && !Auth::user()->hasCustomRole('fabricasoft.desarrollador'))
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.analyst.dashboard') }}" class="nav-link {{ request()->routeIs('fabricasoft.analyst.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-search"></i>
                            <span class="nav-text">Dashboard Analista</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.analyst.solicitudes') }}" class="nav-link {{ request()->routeIs('fabricasoft.analyst.solicitudes') ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span class="nav-text">Solicitudes Asignadas</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.analyst.projects') }}" class="nav-link {{ request()->routeIs('fabricasoft.analyst.projects') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span class="nav-text">Equipos Scrum</span>
                        </a>
                    </div>
                @endif
                
                @if(Auth::user()->hasCustomRole('fabricasoft.desarrollador'))
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.desarrollador.dashboard') }}" class="nav-link {{ request()->routeIs('fabricasoft.desarrollador.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-laptop-code"></i>
                            <span class="nav-text">Dashboard Dev</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.desarrollador.projects') }}" class="nav-link {{ request()->routeIs('fabricasoft.desarrollador.projects') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span class="nav-text">Equipos Scrum</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.desarrollador.ajustes') }}" class="nav-link {{ request()->routeIs('fabricasoft.desarrollador.ajustes*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">Ajustes</span>
                        </a>
                    </div>
                @endif
                
                @if(Auth::user()->hasCustomRole('fabricasoft.cliente_externo') && !Auth::user()->hasCustomRole('fabricasoft.analista') && !Auth::user()->hasCustomRole('fabricasoft.desarrollador'))
                    <div class="nav-item">
                        <a href="{{ route('cliente_externo.dashboard') }}" class="nav-link {{ request()->routeIs('cliente_externo.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('cliente_externo.projects') }}" class="nav-link {{ request()->routeIs('cliente_externo.projects.*') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span class="nav-text">Mis Equipos Scrum</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('cliente_externo.nueva-solicitud') }}" class="nav-link {{ request()->routeIs('cliente_externo.nueva-solicitud*') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i>
                            <span class="nav-text">Nueva Solicitud</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('cliente_externo.ajustes') }}" class="nav-link {{ request()->routeIs('cliente_externo.ajustes') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">Ajustes</span>
                        </a>
                    </div>
                @endif
                
                @if(Auth::user()->hasCustomRole('fabricasoft.cliente_interno') && !Auth::user()->hasCustomRole('fabricasoft.admin') && !Auth::user()->hasCustomRole('fabricasoft.analista') && !Auth::user()->hasCustomRole('fabricasoft.desarrollador'))
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.cliente_interno.dashboard') }}" class="nav-link {{ request()->routeIs('fabricasoft.cliente_interno.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.cliente_interno.projects') }}" class="nav-link {{ request()->routeIs('fabricasoft.cliente_interno.projects*') ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span class="nav-text">Mis Proyectos</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.cliente_interno.nueva-solicitud') }}" class="nav-link {{ request()->routeIs('fabricasoft.cliente_interno.nueva-solicitud*') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i>
                            <span class="nav-text">Nueva Solicitud</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('fabricasoft.cliente_interno.ajustes') }}" class="nav-link {{ request()->routeIs('fabricasoft.cliente_interno.ajustes*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">Ajustes</span>
                        </a>
                    </div>
                @endif
                
            @endauth
            
            <div class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Cerrar Sesión</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @auth
            <!-- Top Navigation -->
            <div class="top-nav">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <h6>{{ Auth::user()->name }}</h6>
                        <small>{{ Auth::user()->email }}</small>
                    </div>
                </div>
            </div>
        @endauth
        
        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personalizados -->
    @stack('scripts')
    
    <script>
        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const logoText = document.getElementById('logoText');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            logoText.classList.toggle('collapsed');
        });
        
        // Auto-hide sidebar on mobile
        function checkMobile() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
                document.getElementById('logoText').classList.add('collapsed');
            }
        }
        
        window.addEventListener('resize', checkMobile);
        checkMobile();
    </script>
</body>
</html>

