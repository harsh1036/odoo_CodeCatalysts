<?php
require_once 'auth/database.php';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = '';
$params = [];
if ($search !== '') {
    $search_sql = "AND (title LIKE ? OR category LIKE ? OR description LIKE ?)";
    $params = array_fill(0, 3, "%$search%");
}

// Fetch all available products (filtered by search if needed)
$stmt = $pdo->prepare("SELECT * FROM items WHERE status = 'Available' $search_sql ORDER BY created_at DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch details for a selected product with uploader info
$selected_product = null;
$uploader_info = null;
if (isset($_GET['id'])) {
    $detail_stmt = $pdo->prepare("
        SELECT i.*, u.name as uploader_name, u.email as uploader_email, u.created_at as uploader_joined
        FROM items i 
        LEFT JOIN users u ON i.owner_id = u.user_id 
        WHERE i.item_id = ?
    ");
    $detail_stmt->execute([$_GET['id']]);
    $selected_product = $detail_stmt->fetch();
    
    if ($selected_product) {
        $uploader_info = [
            'name' => $selected_product['uploader_name'],
            'email' => $selected_product['uploader_email'],
            'joined' => $selected_product['uploader_joined']
        ];
    }
    
    // If search is active and selected product is not in $products, add it to the list
    if ($search !== '' && $selected_product) {
        $found = false;
        foreach ($products as $p) {
            if ($p['item_id'] === $selected_product['item_id']) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $products[] = $selected_product;
        }
    }
}

function getImages($images_json) {
    $images = json_decode($images_json, true);
    if (!$images || !is_array($images)) return [];
    return $images;
}

// Handle swap/redeem actions
$action_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_product) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'swap_request':
                $action_message = '<div class="alert alert-success">Swap request sent successfully!</div>';
                break;
            case 'redeem_points':
                $action_message = '<div class="alert alert-success">Item redeemed successfully for ' . $selected_product['point_value'] . ' points!</div>';
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $selected_product ? htmlspecialchars($selected_product['title']) . ' - ' : '' ?>Item Details - Renewable Cloth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .main-container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 32px 24px 32px;
            margin-top: 32px;
        }
        .header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .search-bar {
            background: #f1f3f6;
            border-radius: 12px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            margin-bottom: 32px;
        }
        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 1.1rem;
        }
        .search-bar .btn {
            border-radius: 8px;
        }
        .product-section {
            display: flex;
            gap: 32px;
            margin-bottom: 32px;
        }
        .image-gallery, .item-details {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 24px;
            min-height: 320px;
        }
        .image-gallery {
            flex: 1 1 45%;
            display: flex;
            flex-direction: column;
        }
        .item-details {
            flex: 1 1 55%;
            display: flex;
            flex-direction: column;
        }
        .main-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        .thumbnail-container {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
        }
        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
        }
        .thumbnail.active {
            border-color: #4CAF50;
        }
        .uploader-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .action-btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .swap-btn {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: none;
            color: white;
        }
        .swap-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }
        .redeem-btn {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
            border: none;
            color: white;
        }
        .redeem-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
        }
        .previous-listings {
            margin-top: 24px;
        }
        .previous-listings-title {
            font-weight: 600;
            margin-bottom: 16px;
        }
        .listing-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .listing-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            width: 160px;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            cursor: pointer;
            transition: box-shadow 0.2s;
        }
        .listing-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        }
        .listing-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        @media (max-width: 991px) {
            .product-section {
                flex-direction: column;
                gap: 20px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container main-container">
    <div class="header-bar">
        <div class="d-flex align-items-center">
            <a href="index.php" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left me-1"></i>Back to Home
            </a>
            <div class="fs-5 fw-bold">
                <?php if ($selected_product): ?>
                    Item Details: <?= htmlspecialchars($selected_product['title']) ?>
                <?php else: ?>
                    Item Details
                <?php endif; ?>
            </div>
        </div>
        <div class="badge bg-secondary">Screen 7</div>
    </div>
    
    <?= $action_message ?>
    
    <form class="search-bar mb-4" method="get" action="product_details.php">
        <input type="text" name="search" placeholder="Search items..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button class="btn btn-outline-secondary ms-2" type="submit"><i class="fas fa-search"></i></button>
    </form>
    
    <?php if ($selected_product): ?>
    <div class="mb-3">
        <a href="product_details.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>Back to All Items
        </a>
    </div>
    <div class="product-section full-width-detail">
        <!-- Image Gallery -->
        <div class="image-gallery">
            <?php $images = getImages($selected_product['images']); ?>
            <?php if (!empty($images)): ?>
                <img src="<?= htmlspecialchars($images[0]) ?>" alt="Main Product Image" class="main-image" id="mainImage">
                <?php if (count($images) > 1): ?>
                    <div class="thumbnail-container">
                        <?php foreach ($images as $index => $img): ?>
                            <img src="<?= htmlspecialchars($img) ?>" alt="Thumbnail" class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                                 onclick="changeMainImage('<?= htmlspecialchars($img) ?>', this)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-muted text-center">
                    <i class="fas fa-image fa-3x mb-3"></i>
                    <div>No images available</div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Item Details -->
        <div class="item-details">
            <!-- Item Status -->
            <div class="mb-3">
                <span class="badge <?= $selected_product['status'] === 'Available' ? 'bg-success' : 'bg-warning' ?> status-badge">
                    <i class="fas fa-circle me-1"></i><?= htmlspecialchars($selected_product['status']) ?>
                </span>
            </div>
            
            <!-- Item Title and Category -->
            <h3 class="mb-2"><?= htmlspecialchars($selected_product['title']) ?></h3>
            <p class="text-muted mb-3">Category: <?= htmlspecialchars($selected_product['category']) ?></p>
            
            <!-- Item Details -->
            <div class="row mb-3">
                <div class="col-6">
                    <strong>Size:</strong> <?= htmlspecialchars($selected_product['size']) ?>
                </div>
                <div class="col-6">
                    <strong>Condition:</strong> <span class="badge bg-success"><?= htmlspecialchars($selected_product['condition']) ?></span>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <strong>Points Value:</strong> <span class="text-primary fw-bold"><?= htmlspecialchars($selected_product['point_value']) ?></span>
                </div>
                <div class="col-6">
                    <strong>Listed:</strong> <?= date('M j, Y', strtotime($selected_product['created_at'])) ?>
                </div>
            </div>
            
            <!-- Full Description -->
            <div class="mb-3">
                <h6 class="fw-bold">Description:</h6>
                <p class="text-muted"><?= nl2br(htmlspecialchars($selected_product['description'])) ?></p>
            </div>
            
            <!-- Tags -->
            <?php if (!empty($selected_product['tags'])): ?>
                <div class="mb-3">
                    <h6 class="fw-bold">Tags:</h6>
                    <?php foreach (json_decode($selected_product['tags'], true) ?? [] as $tag): ?>
                        <span class="badge bg-secondary me-1"><?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Uploader Information -->
            <?php if ($uploader_info): ?>
                <div class="uploader-card">
                    <h6 class="fw-bold mb-2"><i class="fas fa-user me-2"></i>Uploader Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <strong>Name:</strong> <?= htmlspecialchars($uploader_info['name']) ?>
                        </div>
                        <div class="col-6">
                            <strong>Member since:</strong> <?= date('M Y', strtotime($uploader_info['joined'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <form method="POST" class="action-buttons">
                <input type="hidden" name="action" value="swap_request">
                <button type="submit" class="btn action-btn swap-btn">
                    <i class="fas fa-exchange-alt me-2"></i>Swap Request
                </button>
            </form>
            
            <form method="POST" class="action-buttons">
                <input type="hidden" name="action" value="redeem_points">
                <button type="submit" class="btn action-btn redeem-btn">
                    <i class="fas fa-coins me-2"></i>Redeem via Points (<?= htmlspecialchars($selected_product['point_value']) ?>)
                </button>
            </form>
            
        </div>
    </div>
    <style>
    .full-width-detail {
        width: 100%;
        max-width: 100%;
        display: flex;
        flex-direction: row;
        gap: 48px;
        background: none;
        box-shadow: none;
        border-radius: 0;
        padding: 0;
    }
    .full-width-detail .image-gallery,
    .full-width-detail .item-details {
        background: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 0 0 0 !important;
        min-height: unset;
    }
    .full-width-detail .image-gallery {
        flex: 1 1 45%;
        margin-right: 32px;
    }
    .full-width-detail .item-details {
        flex: 1 1 55%;
    }
    @media (max-width: 991px) {
        .full-width-detail {
            flex-direction: column;
            gap: 24px;
        }
        .full-width-detail .image-gallery {
            margin-right: 0;
        }
    }
    .previous-listings { display: none !important; }
    </style>
    <?php endif; ?>
    <?php if (!$selected_product): ?>
    <!-- Previous Listings -->
    <div class="previous-listings">
        <div class="previous-listings-title">Available Items:</div>
        <div class="listing-cards">
            <?php foreach ($products as $product): ?>
                <div class="listing-card" onclick="window.location='product_details.php?id=<?= htmlspecialchars($product['item_id']) ?>'">
                    <?php $imgs = getImages($product['images']); ?>
                    <?php if (!empty($imgs)): ?>
                        <img src="<?= htmlspecialchars($imgs[0]) ?>" alt="Product">
                    <?php else: ?>
                        <i class="fas fa-tshirt fa-2x text-secondary mb-2"></i>
                    <?php endif; ?>
                    <div class="fw-bold" style="font-size:1rem;"> <?= htmlspecialchars($product['title']) ?> </div>
                    <div class="text-muted" style="font-size:0.8rem;"> <?= htmlspecialchars($product['point_value']) ?> points</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function changeMainImage(src, thumbnail) {
    document.getElementById('mainImage').src = src;
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    // Add active class to clicked thumbnail
    thumbnail.classList.add('active');
}
</script>
</body>
</html> 