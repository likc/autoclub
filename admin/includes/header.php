<?php
// Determinar a página atual para destacar no menu
$current_page = basename($_SERVER['PHP_SELF']);

// Verificar se o usuário é admin
function is_admin_role() {
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        return false;
    }
    
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT role FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $is_admin = ($admin['role'] === 'admin');
        $stmt->close();
        $conn->close();
        return $is_admin;
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

$is_admin = is_admin_role();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Admin LTE (tema admin) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .brand-link .brand-image {
            opacity: .8;
            max-height: 40px;
        }
        .user-panel img {
            height: 2.1rem;
            width: 2.1rem;
        }
        .alert {
            margin-bottom: 1rem;
        }
        .btn-group-action {
            white-space: nowrap;
        }
        .main-sidebar {
            background-color: #222;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #d69c1e;
        }
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active {
            background-color: rgba(214, 156, 30, 0.2);
            color: #fff;
        }
        .card-primary:not(.card-outline)>.card-header {
            background-color: #d69c1e;
        }
        .btn-primary {
            background-color: #d69c1e;
            border-color: #c48c18;
        }
        .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: #e6ae30 !important;
            border-color: #d69c1e !important;
        }
        .page-item.active .page-link {
            background-color: #d69c1e;
            border-color: #d69c1e;
        }
        .custom-file-input:focus~.custom-file-label {
            border-color: #e6ae30;
            box-shadow: 0 0 0 0.2rem rgba(214, 156, 30, 0.25);
        }
        .custom-control-input:checked~.custom-control-label::before {
            border-color: #d69c1e;
            background-color: #d69c1e;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Ver Site
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="dashboard.php" class="brand-link">
                <img src="../img/favicon/favicon.svg" alt="AutoClub Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light"><?php echo SITE_NAME; ?></span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['admin_name']); ?>&background=d69c1e&color=fff" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">
                            <?php echo $_SESSION['admin_name']; ?>
                            <small class="d-block text-muted"><?php echo $is_admin ? 'Administrador' : 'Moderador'; ?></small>
                        </a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item <?php echo in_array($current_page, ['cars.php', 'car_add.php', 'car_edit.php']) ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo in_array($current_page, ['cars.php', 'car_add.php', 'car_edit.php']) ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-car"></i>
                                <p>
                                    Veículos
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="cars.php" class="nav-link <?php echo $current_page == 'cars.php' ? 'active' : ''; ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listar Todos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="car_add.php" class="nav-link <?php echo $current_page == 'car_add.php' ? 'active' : ''; ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Adicionar Novo</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item <?php echo in_array($current_page, ['settings.php', 'site_settings.php']) ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo in_array($current_page, ['settings.php', 'site_settings.php']) ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>
                                    Configurações
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="settings.php" class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Meu Perfil</p>
                                    </a>
                                </li>
                                <?php if ($is_admin): ?>
                                <li class="nav-item">
                                    <a href="site_settings.php" class="nav-link <?php echo $current_page == 'site_settings.php' ? 'active' : ''; ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Configurações do Site</p>
                                    </a>
                                </li>
								<li class="nav-item">
                                    <a href="admins.php" class="nav-link <?php echo $current_page == 'admins.php' ? 'active' : ''; ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Administradores</p>
                                    </a>
                                </li>
		                        <li class="nav-item">
                                    <a href="admin_logs.php" class="nav-link <?php echo $current_page == 'admin_logs.php' ? 'active' : ''; ?>">
                                        <i class="nav-icon fas fa-history"></i>
                                        <p>Log de Atividades</p>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Display alerts, if any -->
            <?php if (isset($_SESSION['alert'])): ?>
                <div class="container-fluid mt-3">
                    <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['alert']['message']; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>