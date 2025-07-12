<?php
require_once 'auth/session.php';
require_once 'auth/database.php';

$user = getCurrentUser();

// Fetch available products from database
$stmt = $pdo->prepare("SELECT * FROM items WHERE status = 'Available' ORDER BY created_at DESC LIMIT 8");
$stmt->execute();
$products = $stmt->fetchAll();

function getImages($images_json) {
    $images = json_decode($images_json, true);
    if (!$images || !is_array($images)) return [];
    return $images;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewable Cloth - Sustainable Fashion</title>
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
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .btn-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin: 0 auto 20px;
        }
        
        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer a {
            color: var(--accent-color);
            text-decoration: none;
        }
        
        .footer a:hover {
            color: var(--primary-color);
        }
        
        .user-welcome {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-leaf me-2"></i>Renewable Cloth
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
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
                                <li><a class="dropdown-item" href="auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="nav-link" href="auth/login.php">Login</a>
                        <a class="nav-link" href="auth/signup.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4">
                        Sustainable Fashion for a Better Tomorrow
                    </h1>
                    <p class="lead mb-4">
                        Discover eco-friendly clothing made from renewable materials. 
                        Join the movement towards sustainable fashion that cares for our planet.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#products" class="btn btn-light btn-hero">
                            <i class="fas fa-shopping-cart me-2"></i>Shop Now
                        </a>
                        <a href="#about" class="btn btn-outline-light btn-hero">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-tshirt" style="font-size: 200px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Welcome Message for Logged In Users -->
    <?php if ($user): ?>
    <div class="container mt-4">
        <div class="user-welcome">
            <h4><i class="fas fa-hand-wave me-2"></i>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h4>
            <p class="mb-0">Thank you for choosing sustainable fashion. Explore our latest eco-friendly collection.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Why Choose Renewable Cloth?</h2>
                    <p class="text-muted">We're committed to sustainable fashion practices</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h5>Eco-Friendly Materials</h5>
                        <p class="text-muted">All our products are made from sustainable, renewable materials that are kind to the environment.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <h5>Recycled Content</h5>
                        <p class="text-muted">We use recycled materials wherever possible to reduce waste and conserve resources.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5>Ethical Production</h5>
                        <p class="text-muted">Our products are manufactured under fair labor conditions with respect for workers' rights.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Our Sustainable Collection</h2>
                    <p class="text-muted">Discover our latest eco-friendly fashion items</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card product-card" style="cursor: pointer;" onclick="window.location='product_details.php?id=<?= htmlspecialchars($product['item_id']) ?>'">
                                <div class="card-body text-center">
                                    <?php $imgs = getImages($product['images']); ?>
                                    <?php if (!empty($imgs)): ?>
                                        <img src="<?= htmlspecialchars($imgs[0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="mb-3" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <i class="fas fa-tshirt mb-3" style="font-size: 60px; color: var(--primary-color);"></i>
                                    <?php endif; ?>
                                    <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                    <p class="card-text text-muted"><?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...</p>
                                    <p class="fw-bold text-primary"><?= htmlspecialchars($product['point_value']) ?> Points</p>
                                    <span class="badge bg-success mb-2"><?= htmlspecialchars($product['condition']) ?></span>
                                    <div class="mt-2">
                                        <small class="text-muted">Size: <?= htmlspecialchars($product['size']) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No products available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($products)): ?>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="product_details.php" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>View All Products
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">About Renewable Cloth</h2>
                    <p class="lead mb-4">
                        We believe that fashion should be both beautiful and sustainable. 
                        Our mission is to provide high-quality clothing that doesn't compromise our planet's future.
                    </p>
                    <p class="mb-4">
                        Every product in our collection is carefully selected to meet our strict environmental standards. 
                        From organic cotton to recycled materials, we're committed to reducing the fashion industry's impact on the environment.
                    </p>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">500+</h4>
                            <p class="text-muted">Happy Customers</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary">100%</h4>
                            <p class="text-muted">Sustainable Materials</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-seedling" style="font-size: 200px; color: var(--primary-color); opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Get in Touch</h2>
                    <p class="text-muted">Have questions about our sustainable fashion? We'd love to hear from you!</p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="5" required></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-hero">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-leaf me-2"></i>Renewable Cloth</h5>
                    <p>Sustainable fashion for a better tomorrow. Join us in making the world a greener place, one garment at a time.</p>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#products">Products</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Size Guide</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Newsletter</h6>
                    <p>Subscribe to get updates on new sustainable products!</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Renewable Cloth. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="me-3">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 