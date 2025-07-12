<?php
require_once '../auth/session.php';
require_once '../auth/database.php';
$user = getCurrentUser();
if (!$user || $user['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
// Example user data for demo
$users = [
    ["name" => "John Doe", "email" => "john@example.com", "role" => "user"],
    ["name" => "Jane Smith", "email" => "jane@example.com", "role" => "user"],
    ["name" => "Admin User", "email" => "admin@renewablecloth.com", "role" => "admin"],
    ["name" => "Alice Brown", "email" => "alice@example.com", "role" => "user"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Renewable Cloth</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            color: #222;
            min-height: 100vh;
        }
        .adminpanel-container {
            background: #fff;
            border-radius: 18px;
            margin: 40px auto;
            max-width: 1100px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
            padding: 0;
        }
        .adminpanel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px 0 32px;
        }
        .adminpanel-logo {
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
            letter-spacing: 2px;
        }
        .profile-section {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .profile-name {
            font-weight: 600;
            color: #222;
        }
        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #888;
        }
        .logout-link {
            color: #fff;
            background: #4CAF50;
            border-radius: 8px;
            padding: 8px 18px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .logout-link:hover {
            background: #388e3c;
        }
        .adminpanel-tabs {
            margin: 32px 0 0 0;
            border-bottom: 1.5px solid #e0e0e0;
            padding: 0 32px;
        }
        .nav-tabs .nav-link {
            color: #222;
            font-weight: 500;
            font-size: 1.1rem;
            border: 1px solid #e0e0e0;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            margin-right: 8px;
            background: #f8fafc;
            transition: background 0.2s, color 0.2s;
        }
        .nav-tabs .nav-link.active, .nav-tabs .nav-link:hover {
            background: #e8f5e9;
            color: #388e3c;
            border-bottom: 2px solid #4CAF50;
        }
        .tab-content {
            padding: 32px;
        }
        .user-card-row {
            display: flex;
            align-items: stretch;
            gap: 18px;
            background: #f8fafc;
            border: 1.5px solid #e0e0e0;
            border-radius: 14px;
            margin-bottom: 24px;
            padding: 18px 18px;
        }
        .user-avatar-big {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #888;
            flex-shrink: 0;
        }
        .user-details-box {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 18px;
            margin: 0 18px;
        }
        .user-actions-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
        }
        .admin-action-btn {
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-weight: 500;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .admin-action-btn:hover {
            background: #388e3c;
        }
        @media (max-width: 991.98px) {
            .adminpanel-container {
                margin: 20px 8px;
            }
            .adminpanel-header, .adminpanel-tabs, .tab-content {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
        @media (max-width: 767.98px) {
            .adminpanel-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 18px 10px 0 10px;
            }
            .adminpanel-logo {
                font-size: 1.5rem;
            }
            .profile-section {
                margin-top: 10px;
                width: 100%;
                justify-content: flex-end;
            }
            .adminpanel-tabs {
                padding: 0 5px;
            }
            .tab-content {
                padding: 18px 5px;
            }
            .user-card-row {
                flex-direction: column;
                align-items: stretch;
                padding: 12px 8px;
            }
            .user-details-box {
                margin: 12px 0;
            }
        }
        @media (max-width: 575.98px) {
            .adminpanel-container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }
            .adminpanel-header {
                padding: 12px 2px 0 2px;
            }
            .adminpanel-tabs, .tab-content {
                padding-left: 2px;
                padding-right: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="adminpanel-container">
        <div class="adminpanel-header">
            <div class="adminpanel-logo">Renewable Cloth Admin</div>
            <div class="profile-section">
                <span class="profile-name"><?php echo htmlspecialchars($user['name']); ?></span>
                <div class="profile-avatar"><i class="fas fa-user"></i></div>
                <a href="../auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </div>
        </div>
        <div class="adminpanel-tabs">
            <ul class="nav nav-tabs" id="adminTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Manage Users</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">Manage Orders</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="listings-tab" data-bs-toggle="tab" data-bs-target="#listings" type="button" role="tab" aria-controls="listings" aria-selected="false">Manage Listings</button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="adminTabContent">
            <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
                <h5 class="mb-4">Manage Users</h5>
                <?php foreach ($users as $u): ?>
                <div class="user-card-row">
                    <div class="user-avatar-big"><i class="fas fa-user"></i></div>
                    <div class="user-details-box">
                        <div><strong><?php echo htmlspecialchars($u['name']); ?></strong></div>
                        <div class="text-muted" style="font-size:0.95rem;"><?php echo htmlspecialchars($u['email']); ?></div>
                        <div class="mt-2"><span class="badge bg-secondary"><?php echo htmlspecialchars($u['role']); ?></span></div>
                    </div>
                    <div class="user-actions-box">
                        <button class="admin-action-btn">Actions 1</button>
                        <button class="admin-action-btn">Action 2</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                <h5>Manage Orders</h5>
                <p>Here you can manage orders. (Add your order management UI here.)</p>
            </div>
            <div class="tab-pane fade" id="listings" role="tabpanel" aria-labelledby="listings-tab">
                <h5>Manage Listings</h5>
                <p>Here you can manage listings. (Add your listing management UI here.)</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
