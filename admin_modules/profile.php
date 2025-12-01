<?php
session_start();
include '../config/db.php'; // This includes the $pdo connection object

// Check if logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: ../admin-login.php");
    exit();
}

$email = $_SESSION['admin_email'];

// --- 1. Fetch Admin Info (Using PDO) ---
$stmt = $pdo->prepare("SELECT first_name, last_name, email, phone_num, profile_image, created_at FROM admintbl WHERE email = ?");
$stmt->execute([$email]); 
$admin = $stmt->fetch();

if (!$admin) {
    session_destroy();
    header("Location: ../admin-login.php?error=account_not_found");
    exit();
}

// Format the creation date for display
$formatted_date = isset($admin['created_at']) ? date("Y-m-d H:i:s", strtotime($admin['created_at'])) : 'N/A';
$displayDir = "uploads/"; // Assuming 'uploads/' is in the same directory as this script

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_image'])) {
    // Check if the target directory exists and is writable (assuming it's relative to this script)
    if (!is_dir($displayDir)) {
        mkdir($displayDir, 0777, true);
    }
    
    $fileName = basename($_FILES["profile_image"]["name"]);
    $targetFilePath = $displayDir . $fileName; // Use $displayDir for target path
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allow only image formats
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    if (in_array($fileType, $allowedTypes)) {
        // Move file to uploads directory
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
            
            // --- 2. Update Database (Using PDO syntax) ---
            $stmt = $pdo->prepare("UPDATE admintbl SET profile_image = ? WHERE email = ?");
            // PDO execute handles the binding:
            if ($stmt->execute([$fileName, $email])) { 
                $success = "✅ Profile picture updated successfully!";
                $admin['profile_image'] = $fileName; // Update live data
            } else {
                $error = "⚠️ Failed to update database.";
            }
            // $stmt object is automatically cleaned up in PDO
            
        } else {
            $error = "⚠️ File upload failed. Check folder permissions.";
        }
    } else {
        $error = "❌ Only JPG, JPEG, PNG, & GIF files are allowed.";
    }
}

// Handle profile information update (Edit Information button/modal)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone_num = trim($_POST['phone_num'] ?? '');

    if ($first_name === '' || $last_name === '') {
        $error = 'First name and last name are required.';
    } else {
        $updateStmt = $pdo->prepare("UPDATE admintbl SET first_name = ?, last_name = ?, phone_num = ? WHERE email = ?");
        if ($updateStmt->execute([$first_name, $last_name, $phone_num, $email])) {
            $success = '✅ Profile information updated successfully!';
            // update local copy so page shows new values immediately
            $admin['first_name'] = $first_name;
            $admin['last_name'] = $last_name;
            $admin['phone_num'] = $phone_num;
        } else {
            $error = '⚠️ Failed to update profile information.';
        }
    }
}

// --- 3. FIX: Remove the explicit close() on $conn/PDO. ---
// You were getting the error here (line 56). PDO connections generally stay open 
// until the script ends, or you can use `unset($pdo);` if you absolutely must close it early.
// $conn->close(); // DELETED/COMMENTED OUT to fix line 56 error
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(to right, #e2e2e2, #c9d6ff);
      color: #333;
        }
        
        .profile-container {
            max-width: 900px;
            /* margin: 0 auto; */
            margin-top: 20px;
    margin-bottom: 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .profile-pic-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        
        .profile-pic-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .profile-body {
            padding: 40px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            font-size: 24px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        
        .info-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #212529;
            font-weight: 500;
            margin-top: 2px;
        }
        
        .upload-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .custom-file-upload {
            display: inline-block;
            padding: 12px 30px;
            cursor: pointer;
            background: white;
            border: 2px dashed #667eea;
            border-radius: 10px;
            color: #667eea;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        
        .custom-file-upload:hover {
            background: #667eea;
            color: white;
        }
        
        .custom-file-upload i {
            margin-right: 8px;
        }
        
        input[type="file"] {
            display: none;
        }
        
        .btn-custom {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-warning-custom {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning-custom:hover {
            background: #ffb300;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 193, 7, 0.4);
        }
        
        .btn-secondary-custom {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(108, 117, 125, 0.4);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-buttons button,
        .action-buttons a {
            flex: 1;
            min-width: 200px;
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons button,
            .action-buttons a {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-pic-container">
                <?php if (!empty($admin['profile_image'])): ?>
                    <img src="<?= $displayDir . htmlspecialchars($admin['profile_image']); ?>" alt="Profile Picture" class="profile-pic">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150?text=No+Image" alt="No Image" class="profile-pic">
                <?php endif; ?>
                <div class="profile-pic-overlay">
                    <i class="bi bi-camera-fill text-primary"></i>
                </div>
            </div>
            <div class="profile-name"><?= htmlspecialchars($admin['first_name'] . " " . $admin['last_name']); ?></div>
            <div class="profile-role"><i class="bi bi-shield-check"></i> Administrator</div>
        </div>

        <div class="profile-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error); ?>
                </div>
            <?php elseif (isset($success)): ?>
                <div class="alert alert-success alert-custom">
                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="upload-section">
                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                    <label for="file-upload" class="custom-file-upload">
                        <i class="bi bi-cloud-upload"></i>
                        <span id="file-name">Choose Profile Picture</span>
                    </label>
                    <input id="file-upload" type="file" name="profile_image" accept="image/*" required onchange="updateFileName(this)">
                    <button type="submit" class="btn btn-custom btn-primary-custom w-100 mt-3">
                        <i class="bi bi-upload me-2"></i>Upload New Photo
                    </button>
                </form>
            </div>

            <div class="info-section">
                <div class="section-title">
                    <i class="bi bi-person-circle"></i>
                    <span>Contact Information</span>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">
                            <?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?= htmlspecialchars($admin['email']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?= htmlspecialchars($admin['phone_num']); ?></div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title">
                    <i class="bi bi-info-circle"></i>
                    <span>Account Information</span>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Account Creation Date</div>
                        <div class="info-value"><?= htmlspecialchars($formatted_date); ?></div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil-square me-2"></i>Edit Information
                </button>
                <a href="change_password.php" class="btn btn-custom btn-warning-custom">
                    <i class="bi bi-key-fill me-2"></i>Change Password
                </a>
                <a href="admin_dashboard.php" class="btn btn-custom btn-secondary-custom">
                    <i class="bi bi-arrow-left-circle me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files[0]?.name || 'Choose Profile Picture';
        document.getElementById('file-name').textContent = fileName;
    }
</script>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input class="form-control" name="first_name" value="<?= htmlspecialchars($admin['first_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input class="form-control" name="last_name" value="<?= htmlspecialchars($admin['last_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input class="form-control" name="phone_num" value="<?= htmlspecialchars($admin['phone_num'] ?? ''); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>