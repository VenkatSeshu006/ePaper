<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: views/login.php");
    exit();
}

// Database connection
require_once __DIR__ . '/../../config.php'; // Adjust the path to your config file

// Fetch site settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key_name']] = $row['value'];
}
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?></title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin/assets/css/adminlte.min.css'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <style>
        /* Ensure the footer stays at the bottom */
        html, body {
            height: 100%;
            margin: 0;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Full viewport height */
        }
        .content-wrapper {
            flex: 1; /* Pushes the footer to the bottom */
        }
        .main-footer {
            background-color: #f4f6f9;
            padding: 10px 0;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        /* Navbar User Section */
        .navbar-user {
            display: flex;
            align-items: center;
        }
        .navbar-user .username {
            margin-right: 10px;
            font-weight: bold;
            color: #333;
        }
        .navbar-user .logout-btn {
            color: #dc3545; /* Red color for logout icon */
        }
        .navbar-user .logout-btn:hover {
            color: #b02a37; /* Darker red on hover */
        }

        /* Navbar Site Title */
        .navbar-brand {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }
        .navbar-brand:hover {
            color: #007bff; /* Blue color on hover */
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            /* Mobile navbar adjustments */
            .navbar-brand {
                font-size: 1rem;
                margin-right: 10px;
            }
            
            .navbar-user .username {
                display: none; /* Hide username on mobile to save space */
            }
            
            /* Mobile sidebar improvements */
            .main-sidebar {
                position: fixed;
                z-index: 9999;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar-open .main-sidebar {
                transform: translateX(0);
            }
            
            /* Content wrapper adjustments for mobile */
            .content-wrapper {
                margin-left: 0 !important;
                padding: 10px;
            }
            
            /* Mobile table improvements */
            .table-responsive {
                border: none;
            }
            
            .table td, .table th {
                padding: 8px 4px;
                font-size: 0.85rem;
                white-space: nowrap;
            }
            
            /* Mobile form improvements */
            .form-group {
                margin-bottom: 15px;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 0.9rem;
                margin-bottom: 5px;
            }
            
            /* Card adjustments for mobile */
            .card {
                margin-bottom: 15px;
            }
            
            .card-header {
                padding: 10px 15px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            /* Content header adjustments */
            .content-header h1 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
        }
        
        @media (max-width: 576px) {
            /* Extra small devices adjustments */
            .container-fluid {
                padding: 5px 10px;
            }
            
            .table td, .table th {
                padding: 6px 2px;
                font-size: 0.8rem;
            }
            
            .btn-sm {
                padding: 4px 8px;
                font-size: 0.75rem;
            }
            
            .content-header h1 {
                font-size: 1.3rem;
            }
        }
        
        /* Mobile navigation toggle */
        .mobile-nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #333;
            padding: 8px;
        }
        
        @media (max-width: 768px) {
            .mobile-nav-toggle {
                display: block;
            }
        }
    </style>
    
    <!-- Mobile JavaScript -->
    <script>
        function toggleMobileSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    const sidebar = document.querySelector('.main-sidebar');
                    const toggle = document.querySelector('.mobile-nav-toggle');
                    
                    if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                        document.body.classList.remove('sidebar-open');
                    }
                }
            });
            
            // Close sidebar when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Mobile navigation toggle -->
            <button class="mobile-nav-toggle" onclick="toggleMobileSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Site Title -->
            <a href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin/index.php'; ?>" class="navbar-brand"><?= htmlspecialchars($settings['site_name'] ?? 'Admin Panel') ?></a>

            <!-- User Section (Top Right Corner) -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item navbar-user">
                    <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    <a href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin/logout.php'; ?>" class="nav-link logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">Admin Panel</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <?php 
                    // Generate base URL for admin navigation
                    $base_admin_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin';
                    ?>
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/index.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/editions/index.php" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Editions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/categories/index.php" class="nav-link">
                                <i class="nav-icon fas fa-solid fa-layer-group"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/analytics/index.php" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Analytics</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/users/index.php" class="nav-link">
                                <i class="nav-icon fas fa-solid fa-user"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/pages/index.php" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Pages</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/menus/index.php" class="nav-link">
                                <i class="nav-icon fas fa-regular fa-compass"></i>
                                <p>menus</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/page-settings/index.php" class="nav-link">
                            <i class="nav-icon fas fa-solid fa-brush"></i>
                                <p>Page Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/user-clips/index.php" class="nav-link">
                            <i class="nav-icon fas fa-solid fa-scissors"></i>
                                <p>User Clips</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base_admin_url; ?>/views/settings/index.php" class="nav-link">
                                <i class="nav-icon fas fa-solid fa-gear"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    
                </div>
            </div>