<?php
require_once '../auth/session.php';
require_once '../auth/database.php';
$user = getCurrentUser();
if (!$user) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Renewable Cloth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            color: #222;
            min-height: 100vh;
        }
        .main-container {
            background: #fff;
            border-radius: 18px;
            margin: 40px auto;
            max-width: 1100px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
            padding: 0;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px 0 32px;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
            letter-spacing: 2px;
        }
        .auth-links a {
            margin-left: 18px;
            color: #222;
            font-weight: 500;
            text-decoration: none;
            border-radius: 8px;
            padding: 8px 18px;
            transition: background 0.2s, color 0.2s;
        }
        .auth-links a.btn-login {
            background: transparent;
            border: 1px solid #4CAF50;
        }
        .auth-links a.btn-login:hover {
            background: #4CAF50;
            color: #fff;
        }
        .auth-links a.btn-signup {
            background: #4CAF50;
            color: #fff;
        }
        .auth-links a.btn-signup:hover {
            background: #388e3c;
        }
        .user-dropdown {
            color: #222;
        }
        .intro-section {
            padding: 40px 32px 24px 32px;
            text-align: center;
        }
        .intro-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 12px;
        }
        .intro-desc {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 28px;
        }
        .cta-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: center;
            margin-bottom: 24px;
        }
        .cta-btn {
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 28px;
            border: none;
            transition: background 0.2s, color 0.2s;
        }
        .cta-btn.cta-main {
            background: #4CAF50;
            color: #fff;
        }
        .cta-btn.cta-main:hover {
            background: #388e3c;
        }
        .cta-btn.cta-outline {
            background: #fff;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }
        .cta-btn.cta-outline:hover {
            background: #e8f5e9;
            color: #388e3c;
        }
        .carousel-section {
            margin: 0 32px 32px 32px;
        }
        .carousel-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #388e3c;
            margin-bottom: 12px;
            text-align: center;
        }
        .carousel-item img {
            object-fit: cover;
            width: 100%;
            height: 260px;
            border-radius: 14px;
        }
        .carousel-caption {
            background: rgba(76,175,80,0.85);
            color: #fff;
            border-radius: 8px;
            padding: 10px 18px;
            left: 10%;
            right: 10%;
        }
        .search-bar {
            margin: 24px 32px 0 32px;
        }
        .images-section {
            background: #f1f5f9;
            border-radius: 12px;
            margin: 24px 32px 0 32px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            font-size: 1.5rem;
        }
        .categories-section {
            margin: 32px 32px 0 32px;
        }
        .categories-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #388e3c;
        }
        .category-card {
            background: #f8fafc;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #222;
            font-weight: 500;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
        }
        .category-card:hover {
            background: #e8f5e9;
            color: #388e3c;
        }
        .product-listings {
            margin: 32px 32px 32px 32px;
        }
        .products-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #388e3c;
        }
        .product-card {
            background: #f8fafc;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #222;
            font-weight: 500;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
            position: relative;
        }
        .product-card .badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #7c3aed;
            color: #fff;
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 4px 10px;
        }
        @media (max-width: 991.98px) {
            .main-container {
                margin: 20px 8px;
            }
            .top-bar, .search-bar, .images-section, .categories-section, .product-listings, .carousel-section {
                margin-left: 10px;
                margin-right: 10px;
            }
        }
        @media (max-width: 767.98px) {
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 18px 10px 0 10px;
            }
            .logo {
                font-size: 1.5rem;
            }
            .auth-links {
                margin-top: 10px;
                width: 100%;
                display: flex;
                justify-content: flex-end;
            }
            .intro-section {
                padding: 28px 5px 18px 5px;
            }
            .carousel-section {
                margin: 0 5px 18px 5px;
            }
            .search-bar {
                margin: 18px 5px 0 5px;
            }
            .images-section {
                margin: 18px 5px 0 5px;
                height: 120px;
                font-size: 1.1rem;
            }
            .categories-section, .product-listings {
                margin: 18px 5px 0 5px;
            }
            .category-card {
                height: 60px;
                font-size: 0.95rem;
            }
            .product-card {
                height: 150px;
                font-size: 0.95rem;
            }
        }
        @media (max-width: 575.98px) {
            .main-container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }
            .top-bar {
                padding: 12px 2px 0 2px;
            }
            .search-bar, .images-section, .categories-section, .product-listings, .carousel-section {
                margin-left: 2px;
                margin-right: 2px;
            }
            .categories-title, .products-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="top-bar">
            <div class="logo">Renewable Cloth</div>
            <div class="auth-links">
                <a href="../index.php">Home</a>
                <a href="../items/index.php">Browse</a>
                <?php if (!$user): ?>
                    <a href="../auth/login.php" class="btn-login">Login</a>
                    <a href="../auth/signup.php" class="btn-signup">Sign Up</a>
                <?php else: ?>
                    <a href="../user/userdashboard.php" class="btn btn-outline-success me-2">Dashboard</a>
                    <span class="user-dropdown">Hi, <?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="../auth/logout.php" class="btn-login">Logout</a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Hero Section -->
        <div class="intro-section">
            <div class="intro-title">Sustainable Fashion for a Better Tomorrow</div>
            <div class="intro-desc">Discover eco-friendly clothing, swap your wardrobe, and join the movement for a greener planet.</div>
            <div class="cta-buttons">
                <a href="../items/view_items.php" class="cta-btn cta-main">Start Swapping</a>
                <a href="../items/index.php" class="cta-btn cta-outline">Browse Items</a>
            </div>
        </div>
        <!-- Carousel Section -->
        <div class="carousel-section">
            <div class="carousel-title">Featured Clothing Items</div>
            <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=800&q=80" alt="Featured 1">
                        <div class="carousel-caption">Eco-Friendly Cotton Shirt</div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=800&q=80" alt="Featured 2">
                        <div class="carousel-caption">Recycled Denim Jacket</div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1469398715555-76331a6c7c9b?auto=format&fit=crop&w=800&q=80" alt="Featured 3">
                        <div class="carousel-caption">Organic Linen Dress</div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
        <!-- Images Section (optional, can be used for banners or info) -->
        <div class="images-section">
            <span>Images / Banners / Announcements</span>
        </div>
        <!-- Categories Section -->
        <div class="categories-section">
            <div class="categories-title">Categories</div>
            <div class="row g-3">
                <?php
                $categories = [
                    ['name' => 'Shirt', 'icon' => 'fas fa-tshirt'],
                    ['name' => 'Pants', 'icon' => 'fas fa-socks'],
                    ['name' => 'Jacket', 'icon' => 'fas fa-mitten'],
                    ['name' => 'Dress', 'icon' => 'fas fa-female'],
                    ['name' => 'Shoes', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Accessories', 'icon' => 'fas fa-hat-cowboy'],
                    ['name' => 'Other', 'icon' => 'fas fa-ellipsis-h'],
                ];
                foreach ($categories as $cat): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="category-card d-flex flex-column align-items-center justify-content-center h-100">
                            <i class="<?php echo $cat['icon']; ?> mb-2" style="font-size:1.5rem;"></i>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Product Listings Section -->
        <div class="product-listings">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="products-title">Product Listings</div>
                <?php if ($user): ?>
                    <a href="../items/add_item.php" class="btn btn-success"><i class="fas fa-plus me-1"></i>Add Item</a>
                <?php endif; ?>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                // Fetch latest 8 items for landing page
                $stmt = $pdo->query("SELECT * FROM items WHERE status='Available' ORDER BY created_at DESC LIMIT 8");
                $products = $stmt->fetchAll();
                foreach ($products as $item):
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
                        <img src="<?php echo htmlspecialchars($main_image); ?>" class="card-img-top" alt="Product Image" style="height:180px; object-fit:cover; background:#f8f9fa;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($item['title']); ?></h5>
                            <div class="mb-2">
                                <span class="badge bg-info me-1"><?php echo htmlspecialchars($item['category']); ?></span>
                                <?php $condition_class = 'condition-' . strtolower(str_replace(' ', '-', $item['condition'])); ?>
                                <span class="condition-badge <?php echo $condition_class; ?> ms-2"><?php echo htmlspecialchars($item['condition']); ?></span>
                            </div>
                            <p class="card-text text-muted mb-2" style="min-height:36px;">
                                <?php echo htmlspecialchars(mb_strimwidth($item['description'], 0, 40, '...')); ?>
                            </p>
                            <div class="d-flex align-items-center mt-auto gap-2">
                                <span class="badge bg-warning text-dark me-2"><?php echo $item['point_value']; ?> pts</span>
                                <a href="../items/view_item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye me-1"></i>View</a>
                                <?php if ($user && isset($user['role']) && ($user['id'] === $item['owner_id'] || $user['role'] === 'admin')): ?>
                                    <a href="../items/edit_items.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit me-1"></i>Edit</a>
                                    <a href="../items/delete_items.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');"><i class="fas fa-trash me-1"></i>Delete</a>
                                <?php endif; ?>
                                <?php if ($user): ?>
                                    <a href="../items/redeem_item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-coins me-1"></i>Redeem</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Testimonials/Impact Section (optional) -->
        <div class="mt-5 mb-4 text-center">
            <h5 class="mb-3" style="color:#388e3c;">What Our Users Say</h5>
            <div class="row justify-content-center g-3">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">“I love swapping clothes here! It’s easy and eco-friendly.”<br><span class="fw-bold">- Jane S.</span></div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">“Great way to refresh my wardrobe and help the planet.”<br><span class="fw-bold">- John D.</span></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 