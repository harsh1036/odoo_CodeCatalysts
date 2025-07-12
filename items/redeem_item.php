<?php
require_once '../auth/session.php';
require_once '../auth/database.php';

$user = getCurrentUser();

// Check if user is logged in
if (!$user) {
    header("Location: ../auth/login.php");
    exit();
}

// Get item ID from URL
$item_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($item_id)) {
    header("Location: index.php");
    exit();
}

// Database connection
$host = "localhost";
$dbname = "renewable_cloth";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch item details
$sql = "SELECT i.*, u.name as owner_name FROM items i JOIN users u ON i.owner_id = u.user_id WHERE i.item_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: index.php");
    exit();
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_address = trim($_POST['shipping_address']);
    
    if (empty($shipping_address)) {
        $error_message = "Please provide your shipping address.";
    } elseif ($item['status'] !== 'Available') {
        $error_message = "This item is not available for redemption.";
    } elseif ($user['points'] < $item['point_value']) {
        $error_message = "You don't have enough points to redeem this item.";
    } elseif ($item['owner_id'] === $user['id']) {
        $error_message = "You cannot redeem your own item.";
    } else {
        try {
            $pdo->beginTransaction();
            
            $redemption_id = uniqid();
            
            // Create redemption record
            $sql = "INSERT INTO redemptions (redemption_id, user_id, item_id, points_used, shipping_address) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$redemption_id, $user['id'], $item_id, $item['point_value'], $shipping_address]);
            
            // Update item status
            $sql = "UPDATE items SET status = 'Redeemed' WHERE item_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item_id]);
            
            // Deduct points from user
            $sql = "UPDATE users SET points = points - ? WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item['point_value'], $user['id']]);
            
            // Record point transaction
            $sql = "INSERT INTO point_transactions (transaction_id, user_id, amount, type, description, related_item_id) 
                    VALUES (?, ?, ?, 'spent', ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([uniqid(), $user['id'], -$item['point_value'], 
                          "Redeemed item: " . $item['title'], $item_id]);
            
            $pdo->commit();
            
            $success_message = "Item redeemed successfully! Your order has been placed.";
            
        } catch(PDOException $e) {
            $pdo->rollBack();
            $error_message = "Error processing redemption: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redeem Item - Renewable Cloth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --accent-color: #8BC34A;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        
        .item-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .points-display {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf me-2"></i>Renewable Cloth
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Items</a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <?php if ($user): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user['name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#profile"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#orders"><i class="fas fa-shopping-bag me-2"></i>Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">
                        <i class="fas fa-gift me-3"></i>Redeem Item
                    </h1>
                    <p class="lead mb-0">Use your points to redeem this sustainable item</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="view_item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Item
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Redemption Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <div class="text-center">
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Back to Items
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Item Summary -->
                            <div class="item-summary">
                                <h6 class="mb-3">Item Summary</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="mb-2">
                                            <span class="badge bg-info me-2"><?php echo htmlspecialchars($item['category']); ?></span>
                                            <span class="badge bg-secondary me-2">Size: <?php echo htmlspecialchars($item['size']); ?></span>
                                            <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($item['condition']); ?></span>
                                        </div>
                                        <small class="text-muted">Owner: <?php echo htmlspecialchars($item['owner_name']); ?></small>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="h4 text-primary"><?php echo $item['point_value']; ?> points</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Points Display -->
                            <div class="points-display">
                                <h6 class="mb-2">Your Current Points</h6>
                                <div class="h3 mb-2"><?php echo $user['points']; ?></div>
                                <small class="text-muted">
                                    <?php if ($user['points'] >= $item['point_value']): ?>
                                        <i class="fas fa-check-circle text-success me-1"></i>Sufficient points available
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger me-1"></i>Insufficient points
                                    <?php endif; ?>
                                </small>
                            </div>
                            
                            <!-- Redemption Form -->
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label">Shipping Address *</label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="4" 
                                              placeholder="Enter your complete shipping address..." required><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; ?></textarea>
                                    <small class="text-muted">This address will be used to ship your redeemed item.</small>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Important:</strong> By redeeming this item, you agree to pay <?php echo $item['point_value']; ?> points. 
                                    The item will be shipped to the address you provide above.
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="view_item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <?php if ($user['points'] >= $item['point_value']): ?>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-gift me-2"></i>Confirm Redemption
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-primary" disabled>
                                            <i class="fas fa-gift me-2"></i>Insufficient Points
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 