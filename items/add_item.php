<?php
require_once '../auth/session.php';
require_once '../auth/database.php';

$user = getCurrentUser();

// Check if user is logged in
if (!$user) {
    header("Location: ../auth/login.php");
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

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $size = $_POST['size'];
    $condition = $_POST['condition'];
    $point_value = (int)$_POST['point_value'];
    $tags = $_POST['tags'];
    
    if (empty($title) || empty($description) || empty($category) || empty($size) || empty($condition)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            $item_id = uniqid();
            
            // Process tags - convert comma-separated string to JSON array
            $tags_array = [];
            if (!empty($tags)) {
                $tags_array = array_map('trim', explode(',', $tags));
                $tags_array = array_filter($tags_array); // Remove empty tags
            }
            $tags_json = !empty($tags_array) ? json_encode($tags_array) : null;
            
            // Insert into items table
            $sql = "INSERT INTO items (item_id, owner_id, title, description, category, size, `condition`, tags, images, point_value) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item_id, $user['id'], $title, $description, $category, $size, $condition, $tags_json, null, $point_value]);
            
            $success_message = "Item added successfully! It will be reviewed by an admin before being made available.";
            
            // Clear form data
            $_POST = array();
            
        } catch(PDOException $e) {
            $error_message = "Error adding item: " . $e->getMessage();
        }
    }
}

$categories = ['Shirt', 'Pants', 'Jacket', 'Dress', 'Shoes', 'Accessories', 'Other'];
$sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];
$conditions = ['New', 'Like New', 'Good', 'Fair', 'Used'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - Renewable Cloth</title>
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
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
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
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .required::after {
            content: " *";
            color: #dc3545;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
            padding: 20px;
            text-align: center;
            color: #6c757d;
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
                    <li class="nav-item">
                        <a class="nav-link active" href="add_item.php">Add Item</a>
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
                        <i class="fas fa-plus me-3"></i>Add New Item
                    </h1>
                    <p class="lead mb-0">Share your sustainable clothing with the community</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="index.php" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Items
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
                            <i class="fas fa-tshirt me-2"></i>Item Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label required">Item Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               placeholder="Enter item title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category" class="form-label required">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat; ?>" <?php echo (isset($_POST['category']) && $_POST['category'] === $cat) ? 'selected' : ''; ?>>
                                                    <?php echo $cat; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label required">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Describe your item in detail..." required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="size" class="form-label required">Size</label>
                                        <select class="form-select" id="size" name="size" required>
                                            <option value="">Select Size</option>
                                            <?php foreach ($sizes as $size): ?>
                                                <option value="<?php echo $size; ?>" <?php echo (isset($_POST['size']) && $_POST['size'] === $size) ? 'selected' : ''; ?>>
                                                    <?php echo $size; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="condition" class="form-label required">Condition</label>
                                        <select class="form-select" id="condition" name="condition" required>
                                            <option value="">Select Condition</option>
                                            <?php foreach ($conditions as $cond): ?>
                                                <option value="<?php echo $cond; ?>" <?php echo (isset($_POST['condition']) && $_POST['condition'] === $cond) ? 'selected' : ''; ?>>
                                                    <?php echo $cond; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="point_value" class="form-label">Point Value</label>
                                        <input type="number" class="form-control" id="point_value" name="point_value" 
                                               placeholder="0" min="0" value="<?php echo isset($_POST['point_value']) ? htmlspecialchars($_POST['point_value']) : '0'; ?>">
                                        <small class="text-muted">Points users need to redeem this item</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags" 
                                       placeholder="Enter tags separated by commas (e.g., eco-friendly, cotton, vintage)" 
                                       value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>">
                                <small class="text-muted">Help others find your item with relevant tags</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Item Images</label>
                                <div class="preview-image">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p>Drag and drop images here or click to browse</p>
                                    <input type="file" class="form-control" multiple accept="image/*">
                                </div>
                                <small class="text-muted">Upload clear images of your item (optional)</small>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Add Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 