<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('images/Favicon2.png')}}" type="image/x-icon">
    <title>Fabrica de software</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sena-green: #39B54A;
            --sena-dark-green: #2E8B3E;
            --sena-light-green: #D1E7DD;
        }
        .main-sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            background-color: white;
            border-right: 1px solid #dee2e6;
            width: 250px;
        }
        .content-wrapper {
            margin-left: 250px;
            background-color: #ffffff;
            min-height: calc(100vh - 56px - 52px);
        }
        .hover-green:hover {
            color: var(--sena-green) !important;
            transition: color 0.3s ease-in-out;
        }
        .brand-link {
            border-bottom: 3px solid var(--sena-green);
            background-color: var(--sena-green);
            color: white;
            padding: 10px;
        }
        .brand-text {
            color: white !important;
            font-weight: bold !important;
        }
        .nav-sidebar .nav-item > .nav-link {
            color: #495057;
        }
        .nav-sidebar .nav-item > .nav-link.active,
        .nav-sidebar .nav-item > .nav-link:hover {
            background-color: var(--sena-light-green);
            color: var(--sena-dark-green);
        }
        .navbar-dark {
            background-color: var(--sena-dark-green) !important;
            padding: 5px 10px;
        }
        .main-footer {
            background-color: var(--sena-dark-green) !important;
            color: white !important;
            padding: 10px;
            text-align: center;
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: var(--sena-green);
            color: white;
        }
        .card {
            border-top: 3px solid var(--sena-green);
            margin: 20px;
        }
        .btn-primary {
            background-color: var(--sena-green);
            border-color: var(--sena-dark-green);
        }
        .btn-primary:hover {
            background-color: var(--sena-dark-green);
            border-color: var(--sena-dark-green);
        }
        .bg-primary {
            background-color: var(--sena-green) !important;
        }
        .user-panel {
            padding: 10px;
        }
        .content-header {
            padding: 15px 20px;
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }
        .table {
            margin: 20px;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="{{ asset('images/images.png') }}" alt="AdminLTELogo" height="100" width="150">
        </div>

        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" role="button">
                        <i class="fas fa-bell"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        admin
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> {{ __('Cerrar Sesión') }}
                        </a>
                    </div>
                    <form id="logout-form" action="#" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <!-- Brand Logo -->
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">Fabrica de software</span>
            </a>

            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block text-dark">Administrador</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Inicio -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-home text-green mr-2"></i>
                                <p>Inicio</p>
                            </a>
                        </li>
                        <!-- Registro -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-user-plus text-green mr-2"></i>
                                <p>Registro</p>
                            </a>
                        </li>
                        <!-- desarrolladores -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users text-green mr-2"></i>
                                <p>desarrolladores</p>
                            </a>
                        </li>
                        <!-- SERVICIOS -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cogs text-green mr-2"></i>
                                <p>
                                    SERVICIOS
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fas fa-headset nav-icon text-green mr-2"></i>
                                        <p>Solicitudes y soporte</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fas fa-laptop-code nav-icon text-green mr-2"></i>
                                        <p>Programadores</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <h1>Panel de Administración</h1>
            </div>
            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Usuarios</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm">usuarios</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>david</td>
                                    <td>davididiiaz182005@gmail.co</td>
                                    <td>Administrador</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">editar</button>
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>

        <footer class="main-footer">
            <strong>Copyright © 2024-2025
                <a href="#" style="color: white;">Fabrica de software - SENA</a>.
            </strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('AdminLTE/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('AdminLTE/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminLTE/dist/js/adminlte.js') }}"></script>
    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="{{ asset('AdminLTE/plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('AdminLTE/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('AdminLTE/dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo -->
    <script src="{{ asset('AdminLTE/dist/js/pages/dashboard2.js') }}"></script>
</body>
</html>
