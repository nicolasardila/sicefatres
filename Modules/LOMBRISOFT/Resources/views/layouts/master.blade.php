<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('images/Favicon2.png')}}" type="image/x-icon">
    <title>Gestion de Unidad de Lombricultivo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
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
        }

        .content-wrapper {
            margin-left: 250px;
            background-color: #f8f9fa;
        }

        .hover-green:hover {
            color: var(--sena-green) !important;
            transition: color 0.3s ease-in-out;
        }

        .brand-link {
            border-bottom: 3px solid var(--sena-green);
        }

        .brand-text {
            color: var(--sena-dark-green) !important;
            font-weight: bold !important;
        }

        .nav-sidebar .nav-item>.nav-link {
            color: #495057;
        }

        .nav-sidebar .nav-item>.nav-link.active,
        .nav-sidebar .nav-item>.nav-link:hover {
            background-color: var(--sena-light-green);
            color: var(--sena-dark-green);
        }

        .navbar-dark {
            background-color: var(--sena-dark-green) !important;
        }

        .main-footer {
            background-color: var(--sena-dark-green) !important;
            color: white !important;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: var(--sena-green);
            color: white;
        }

        .card {
            border-top: 3px solid var(--sena-green);
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

        .navbar-badge {
            font-size: 0.8rem;
            padding: 2px 6px;
        }

        .dropdown-item {
            white-space: normal;
            /* Permite que el texto se ajuste si es largo */
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="{{ asset('./adminLTE/dist/img/logoS.png') }}" alt="AdminLTELogo" height="100" width="150">
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
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-warning navbar-badge" id="alert-count">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end" aria-labelledby="alertsDropdown">
                        <li>
                            <h6 class="dropdown-header">Alertas por Vencer</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li id="alert-list">
                            <a class="dropdown-item text-center">Cargando alertas...</a>
                        </li>
                    </ul>
                </li>




                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('Cerrar Sesión') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <!-- Brand Logo -->
            <a href="#" class="brand-link">
                <img class="" src="{{ asset('./adminLTE/dist/img/logoS.png') }}" alt="AdminLTELogo" height="35" width="55">
                <span class="brand-text font-weight-light">Lombrisoft</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('lombrisoft.admin.welcome') }}" class="nav-link">
                                <i class="nav-icon fas fa-home text-green"></i>
                                <p>Inicio</p>
                            </a>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-box text-green"></i>
                                <p>
                                    Camas Lombricultivo
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.camas.create') }}" class="nav-link">
                                        <i class="fas fa-edit nav-icon text-green"></i>
                                        <p>Ingreso</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.camas.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon text-green"></i>
                                        <p>Listas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tasks text-green"></i>
                                <p>
                                    Actividades
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.bed_activities.create') }}" class="nav-link">
                                        <i class="fas fa-edit nav-icon text-green"></i>
                                        <p>Ingreso</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.bed_activities.index') }}" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon text-green"></i>
                                        <p>Listas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-alt text-green"></i>
                                <p>
                                    Reportes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('reports.index') }}">
                                        <i class="fas fa-tasks nav-icon text-green"></i>
                                        <p>Historial</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-bell text-orange"></i>
                                <p>
                                    Alertas
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.activity_alerts.create') }}" class="nav-link">
                                        <i class="fas fa-plus-circle nav-icon text-orange"></i>
                                        <p>Nueva Alerta</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.activity_alerts.index') }}" class="nav-link">
                                        <i class="fas fa-list nav-icon text-orange"></i>
                                        <p>Lista de Alertas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.activity_alerts.index', ['estado' => 'vencidas']) }}" class="nav-link">
                                        <i class="fas fa-exclamation-triangle nav-icon text-red"></i>
                                        <p>Alertas Vencidas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('lombrisoft.admin.activity_alerts.index', ['estado' => 'proximas']) }}" class="nav-link">
                                        <i class="fas fa-clock nav-icon text-yellow"></i>
                                        <p>Alertas Próximas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>

        <footer class="main-footer" style="width: 100%; position: fixed; bottom: 0; left: 0;">
            <strong>Copyright © 2023-2025
                <a href="#" style="color: white;">Lombrisoft - SENA</a>.
            </strong>
            Todos los derechos reservados.
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
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('AdminLTE/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminLTE/dist/js/adminlte.js') }}"></script>
    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="{{ asset('AdminLTE/plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('AdminLTE-/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('AdminLTE/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('AdminLTE/dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo -->
    <script src="{{ asset('AdminLTE/dist/js/pages/dashboard2.js') }}"></script>

    <!-- Agrega Moment.js -->
    <script src="https://momentjs.com/downloads/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadAlerts() {
                $.getJSON('{{ route("lombrisoft.admin.activity_alerts.send") }}', function(data) {
                    $('#alert-count').text(data.count);
                    var alertList = $('#alert-list');
                    alertList.empty();

                    if (data.count > 0) {
                        data.alerts.forEach(function(alert) {
                            var nextDate = alert.next_expected ? moment(alert.next_expected).format('DD/MM/YYYY') : 'Sin fecha';
                            alertList.append(
                                '<a href="#" class="dropdown-item">' +
                                '<i class="fas fa-exclamation-triangle me-2"></i> ' + alert.wormBed.nombre + ' - ' + alert.activity_type +
                                '<span class="float-end text-muted">' + nextDate + '</span>' +
                                '</a>'
                            );
                        });
                    } else {
                        alertList.append('<a class="dropdown-item text-center">No hay alertas por vencer</a>');
                    }
                }).fail(function() {
                    $('#alert-count').text('0');
                    $('#alert-list').html('<a class="dropdown-item text-center">Error al cargar</a>');
                });
            }

            loadAlerts();
            setInterval(loadAlerts, 300000); // 5 min
        });
    </script>
</body>

</html>