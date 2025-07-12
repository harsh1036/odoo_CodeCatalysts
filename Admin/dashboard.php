<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?error=Access denied. Admin privileges required.");
    exit();
}

require_once '../config/database.php';

// Get statistics
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
    
    // Total items
    $stmt = $db->query("SELECT COUNT(*) as total_items FROM items");
    $total_items = $stmt->fetch(PDO::FETCH_ASSOC)['total_items'];
    
    // Pending approvals
    $stmt = $db->query("SELECT COUNT(*) as pending_items FROM items WHERE is_approved = 0");
    $pending_items = $stmt->fetch(PDO::FETCH_ASSOC)['pending_items'];
    
    // Total swaps
    $stmt = $db->query("SELECT COUNT(*) as total_swaps FROM swap_requests");
    $total_swaps = $stmt->fetch(PDO::FETCH_ASSOC)['total_swaps'];
    
} catch (PDOException $e) {
    $error = "Database error occurred";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ReWear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4><i class="fas fa-recycle"></i> ReWear</h4>
                        <p class="mb-0">Admin Panel</p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a class="nav-link" href="items.php">
                            <i class="fas fa-tshirt"></i> Manage Items
                        </a>
                        <a class="nav-link" href="approvals.php">
                            <i class="fas fa-check-circle"></i> Pending Approvals
                            <?php if ($pending_items > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo $pending_items; ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="swaps.php">
                            <i class="fas fa-exchange-alt"></i> Swap Requests
                        </a>
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                        <hr class="my-3">
                        <a class="nav-link" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">Admin Dashboard</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="main-content">
                    <!-- Welcome Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-sun text-warning"></i> 
                                        Welcome back, <?php echo $_SESSION['first_name']; ?>!
                                    </h4>
                                    <p class="card-text text-muted">Here's what's happening with ReWear today.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="text-primary"><?php echo $total_users; ?></h3>
                                <p class="text-muted mb-0">Total Users</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-success">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <h3 class="text-success"><?php echo $total_items; ?></h3>
                                <p class="text-muted mb-0">Total Items</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3 class="text-warning"><?php echo $pending_items; ?></h3>
                                <p class="text-muted mb-0">Pending Approvals</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-info">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <h3 class="text-info"><?php echo $total_swaps; ?></h3>
                                <p class="text-muted mb-0">Total Swaps</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="approvals.php" class="btn btn-outline-primary">
                                            <i class="fas fa-check-circle"></i> Review Pending Items
                                        </a>
                                        <a href="users.php" class="btn btn-outline-success">
                                            <i class="fas fa-users"></i> Manage Users
                                        </a>
                                        <a href="reports.php" class="btn btn-outline-info">
                                            <i class="fas fa-chart-bar"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-user-plus text-success"></i> New user registered</span>
                                                <small class="text-muted">2 min ago</small>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-tshirt text-primary"></i> New item uploaded</span>
                                                <small class="text-muted">15 min ago</small>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-exchange-alt text-info"></i> Swap request made</span>
                                                <small class="text-muted">1 hour ago</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 