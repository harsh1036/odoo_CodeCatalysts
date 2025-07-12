<?php
require_once '../auth/session.php';
require_once '../auth/database.php';
$user = getCurrentUser();
if (!$user) {
    header("Location: auth/login.php");
    exit();
}
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
// Fetch user profile info
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
// Fetch user's listings
$stmt = $pdo->prepare("SELECT * FROM items WHERE owner_id = ? AND status = 'Available' ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$listings = $stmt->fetchAll();
// Fetch user's purchases (redemptions)
$stmt = $pdo->prepare("SELECT r.*, i.title, i.images, i.category, i.point_value FROM redemptions r JOIN items i ON r.item_id = i.item_id WHERE r.user_id = ? AND i.status = 'Available' ORDER BY r.created_at DESC");
$stmt->execute([$user['id']]);
$purchases = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Renewable Cloth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .dashboard-header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 40px 0 30px 0; margin-bottom: 30px; }
        .profile-card { background: #fff; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.07); padding: 32px; margin-bottom: 32px; }
        .avatar { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; background: #e0e0e0; }
        .profile-info { margin-left: 32px; }
        .profile-stats { margin-top: 18px; }
        .profile-stat { display: inline-block; margin-right: 32px; font-size: 1.1rem; }
        .profile-stat .stat-label { color: #888; font-size: 0.95rem; }
        .section-title { font-size: 1.3rem; font-weight: 600; color: #388e3c; margin-bottom: 18px; }
        .product-card { background: #f8fafc; border: 1px solid #e0e0e0; border-radius: 12px; height: 220px; display: flex; align-items: center; justify-content: center; color: #222; font-weight: 500; font-size: 1rem; transition: background 0.2s, color 0.2s; cursor: pointer; position: relative; }
        .product-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px 10px 0 0; background: #f8f9fa; }
        .product-card .card-body { padding: 12px; }
        .product-card .card-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 4px; }
        .product-card .badge { font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="dashboard-header text-center">
        <h1 class="fw-bold mb-2">User Dashboard</h1>
        <p class="lead mb-0">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</p>
    </div>
    <div class="container">
        <!-- Profile Section -->
        <div class="profile-card d-flex align-items-center">
            <img src="<?php echo $profile && $profile['avatar_url'] ? htmlspecialchars($profile['avatar_url']) : 'https://via.placeholder.com/110x110?text=Avatar'; ?>" class="avatar me-4" alt="Avatar">
            <div class="profile-info">
                <h3 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h3>
                <div class="mb-2 text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                <div class="profile-stats">
                    <span class="profile-stat"><b><?php echo count($listings); ?></b> <span class="stat-label">Listings</span></span>
                    <span class="profile-stat"><b><?php echo count($purchases); ?></b> <span class="stat-label">Purchases</span></span>
                    <span class="profile-stat"><b><?php echo isset($user['points']) ? $user['points'] : 0; ?></b> <span class="stat-label">Points</span></span>
                </div>
                <?php if ($profile && $profile['bio']): ?>
                    <div class="mt-2 text-muted"><?php echo htmlspecialchars($profile['bio']); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <!-- My Listings -->
        <div class="mb-5">
            <div class="section-title">My Listings</div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($listings as $item):
                    $images = [];
                    if (!empty($item['images'])) {
                        $decoded = json_decode($item['images'], true);
                        if (is_array($decoded)) {
                            $images = $decoded;
                        }
                    }
                    $main_image = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x500?text=No+Image';
                ?>
                <div class="col">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($main_image); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($item['title']); ?></h5>
                            <div class="mb-2">
                                <span class="badge bg-info me-1"><?php echo htmlspecialchars($item['category']); ?></span>
                                <?php $status_class = 'status-' . strtolower($item['status']); ?>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($item['status']); ?></span>
                            </div>
                            <p class="card-text text-muted mb-2" style="min-height:36px;">
                                <?php echo htmlspecialchars(mb_strimwidth($item['description'], 0, 40, '...')); ?>
                            </p>
                            <div class="d-flex align-items-center mt-auto">
                                <span class="badge bg-warning text-dark me-2"><?php echo $item['point_value']; ?> pts</span>
                                <a href="/odoo1/items/view_items.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-primary ms-auto">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- My Purchases -->
        <div class="mb-5">
            <div class="section-title">My Purchases</div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($purchases as $item):
                    $images = [];
                    if (!empty($item['images'])) {
                        $decoded = json_decode($item['images'], true);
                        if (is_array($decoded)) {
                            $images = $decoded;
                        }
                    }
                    $main_image = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x500?text=No+Image';
                ?>
                <div class="col">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($main_image); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($item['title']); ?></h5>
                            <div class="mb-2">
                                <span class="badge bg-info me-1"><?php echo htmlspecialchars($item['category']); ?></span>
                                <span class="badge bg-success ms-2">Redeemed</span>
                            </div>
                            <p class="card-text text-muted mb-2" style="min-height:36px;">
                                <?php echo htmlspecialchars(mb_strimwidth($item['description'] ?? '', 0, 40, '...')); ?>
                            </p>
                            <div class="d-flex align-items-center mt-auto">
                                <span class="badge bg-warning text-dark me-2"><?php echo $item['point_value']; ?> pts</span>
                                <a href="/odoo1/items/view_items.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-primary ms-auto">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 