<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Please login to access your dashboard.");
    exit();
}

require_once '../config/database.php';

// Get user data and statistics
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $user_id = $_SESSION['user_id'];
    
    // Get user's items count
    $stmt = $db->prepare("SELECT COUNT(*) as my_items FROM items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $my_items = $stmt->fetch(PDO::FETCH_ASSOC)['my_items'];
    
    // Get user's swap requests count
    $stmt = $db->prepare("SELECT COUNT(*) as my_swaps FROM swap_requests WHERE requester_id = ?");
    $stmt->execute([$user_id]);
    $my_swaps = $stmt->fetch(PDO::FETCH_ASSOC)['my_swaps'];
    
    // Get recent items
    $stmt = $db->prepare("SELECT * FROM items WHERE is_approved = 1 AND is_available = 1 ORDER BY created_at DESC LIMIT 6");
    $stmt->execute();
    $recent_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Database error occurred";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - ReWear</title>
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
        .item-card {
            transition: transform 0.3s;
        }
        .item-card:hover {
            transform: translateY(-5px);
        }
        .points-badge {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            font-weight: bold;
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
                        <p class="mb-0">User Panel</p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <a class="nav-link" href="my-items.php">
                            <i class="fas fa-tshirt"></i> My Items
                        </a>
                        <a class="nav-link" href="browse.php">
                            <i class="fas fa-search"></i> Browse Items
                        </a>
                        <a class="nav-link" href="add-item.php">
                            <i class="fas fa-plus"></i> Add New Item
                        </a>
                        <a class="nav-link" href="swap-history.php">
                            <i class="fas fa-exchange-alt"></i> Swap History
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
                        <span class="navbar-brand">User Dashboard</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item me-3">
                                <span class="badge points-badge fs-6">
                                    <i class="fas fa-coins"></i> <?php echo $_SESSION['points']; ?> Points
                                </span>
                            </div>
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
                                    <p class="card-text text-muted">Ready to make fashion sustainable? Start swapping today!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <h3 class="text-primary"><?php echo $_SESSION['points']; ?></h3>
                                <p class="text-muted mb-0">Available Points</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-success">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <h3 class="text-success"><?php echo $my_items; ?></h3>
                                <p class="text-muted mb-0">My Items</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-info">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <h3 class="text-info"><?php echo $my_swaps; ?></h3>
                                <p class="text-muted mb-0">My Swaps</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="add-item.php" class="btn btn-outline-primary">
                                            <i class="fas fa-plus"></i> Add New Item
                                        </a>
                                        <a href="browse.php" class="btn btn-outline-success">
                                            <i class="fas fa-search"></i> Browse Items
                                        </a>
                                        <a href="my-items.php" class="btn btn-outline-info">
                                            <i class="fas fa-tshirt"></i> Manage My Items
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-lightbulb"></i> How It Works</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex">
                                                <span class="badge bg-primary me-2">1</span>
                                                <span>Upload your unused clothes</span>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex">
                                                <span class="badge bg-primary me-2">2</span>
                                                <span>Browse items from others</span>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex">
                                                <span class="badge bg-primary me-2">3</span>
                                                <span>Request swaps or use points</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Items -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-fire"></i> Recently Added Items</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if (!empty($recent_items)): ?>
                                            <?php foreach ($recent_items as $item): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card item-card h-100">
                                                        <div class="card-body">
                                                            <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                            <p class="card-text text-muted small">
                                                                <?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?>
                                                            </p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($item['category']); ?></span>
                                                                <span class="badge bg-warning"><?php echo htmlspecialchars($item['condition_rating']); ?></span>
                                                            </div>
                                                            <?php if ($item['points_required'] > 0): ?>
                                                                <div class="mt-2">
                                                                    <span class="badge points-badge">
                                                                        <i class="fas fa-coins"></i> <?php echo $item['points_required']; ?> points
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="card-footer bg-transparent">
                                                            <a href="item-detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                                                View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12 text-center">
                                                <p class="text-muted">No items available yet. Be the first to add an item!</p>
                                                <a href="add-item.php" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add First Item
                                                </a>
                                            </div>
                                        <?php endif; ?>
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