<?php
session_start();
require_once '../config/db.php';

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../authentications/student_login.html');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student info
$stmt = $pdo->prepare("SELECT Matric_No, first_name, last_name, email, Phone_Num, Department, Level, Gender, created_at, profile_image FROM studenttbl WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    session_unset();
    session_destroy();
    header('Location: ../authentications/student_login.html');
    exit();
}

$full_name = $student['first_name'] . ' ' . $student['last_name'];
$formatted_date = isset($student['created_at']) ? date('Y-m-d H:i:s', strtotime($student['created_at'])) : 'N/A';


// Handle profile image upload
$upload_success = null;
$upload_error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_image'])) {
    $targetDir = "uploads/students/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowedTypes = ["jpg", "jpeg", "png"];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
            $stmt = $pdo->prepare("UPDATE studenttbl SET profile_image = ? WHERE student_id = ?");
            if ($stmt->execute([$fileName, $student_id])) { 
                $upload_success = "Profile picture updated successfully!";
                $student['profile_image'] = $fileName;
            } else {
                $upload_error = "Failed to update database.";
            }
        } else {
            $upload_error = "File upload failed. Check folder permissions.";
        }
    } else {
        $upload_error = "Only JPG, JPEG, PNG files are allowed.";
    }
}

// Set profile image path
$profile_image_path = !empty($student['profile_image']) ? 
    "uploads/students/" . htmlspecialchars($student['profile_image']) : null;


// Generate initials for placeholder avatar
$firstInitial = mb_substr($student['first_name'], 0, 1, 'UTF-8');
$lastInitial  = mb_substr($student['last_name'], 0, 1, 'UTF-8');
$student_initials = strtoupper($firstInitial . $lastInitial);

// Handle contact info update (no academic info editing here)
$contact_success = null;
$contact_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact'])) {
    $new_email = trim($_POST['email'] ?? '');
    $new_phone = trim($_POST['phone_num'] ?? '');

    if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $contact_errors[] = 'Please enter a valid email address.';
    }

    if (empty($new_phone) || !preg_match('/^\+?[0-9\s\-\(\)]+$/', $new_phone)) {
        $contact_errors[] = 'Please enter a valid phone number.';
    }

    if (empty($contact_errors)) {
        // Ensure email is unique among students (excluding this one)
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM studenttbl WHERE email = ? AND student_id != ?");
        $checkStmt->execute([$new_email, $student_id]);
        if ($checkStmt->fetchColumn() > 0) {
            $contact_errors[] = 'Email is already in use by another student.';
        } else {
            $updateStmt = $pdo->prepare("UPDATE studenttbl SET email = ?, Phone_Num = ? WHERE student_id = ?");
            if ($updateStmt->execute([$new_email, $new_phone, $student_id])) {
                $contact_success = 'Contact information updated successfully.';
                // Refresh current data
                $student['email'] = $new_email;
                $student['Phone_Num'] = $new_phone;
            } else {
                $contact_errors[] = 'Failed to update contact information. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
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
            /* background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); */
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
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }
        .custom-file-upload {
            border: 2px dashed #667eea;;
            padding: 15px;
            cursor: pointer;
            display: block;
            width: 100%;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-align: center;
            /* background: #f8f9fa; */
            color: #667eea;
        }
        .custom-file-upload:hover {
            background: #667eea;
            color: white;
            /* border-color: #93f9b9;
            background: #e9f7ef; */
        }
        .hidden-file-input {
            opacity: 0;
            position: absolute;
            width: 0;
            height: 0;
            overflow: hidden;
        }
        .upload-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        .profile-name {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .profile-role {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 400;
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
            color: #667eea;;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            font-size: 22px;
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
            /* background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); */
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
        }
        .info-content { flex: 1; }
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
        .btn-custom {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            /* background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); */
            color: white;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(29, 151, 108, 0.4);
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
        .btn-outline-custom {
            border-radius: 10px;
            font-weight: 600;
        }
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .action-buttons a,
        .action-buttons button {
            flex: 1;
            min-width: 210px;
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

/* Custom Styles for the Beautiful Button */
.edit-contact-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: #ffffff;
    font-weight: 700;
    letter-spacing: 0.8px;
    padding: 15px 35px;
    border-radius: 50px;
    text-transform: uppercase;
    font-size: 14px;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.edit-contact-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.edit-contact-btn:hover::before {
    left: 100%;
}

.edit-contact-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.edit-contact-btn:active {
    transform: translateY(-1px) scale(1.02);
    transition: all 0.1s;
}
        
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
            .action-buttons a,
            .action-buttons button {
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
                <?php if ($profile_image_path && file_exists($profile_image_path)): ?>
                    <img src="<?php echo $profile_image_path; ?>" class="profile-avatar" alt="Profile Picture">
                <?php else: ?>
                    <div class="profile-avatar">
                        <?php echo htmlspecialchars($student_initials); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-name"><?php echo htmlspecialchars($full_name); ?></div>
            <div class="profile-role">
                <i class="bi bi-mortarboard-fill"></i>
                Student • <?php echo htmlspecialchars($student['Department']); ?> • Level <?php echo htmlspecialchars($student['Level']); ?>
            </div>
        </div>

        <div class="profile-body">
            <?php if (!empty($upload_error)): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo htmlspecialchars($upload_error); ?>
                </div>
            <?php elseif (!empty($upload_success)): ?>
                <div class="alert alert-success alert-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo htmlspecialchars($upload_success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($contact_errors)): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo htmlspecialchars(implode(' ', $contact_errors)); ?>
                </div>
            <?php elseif (!empty($contact_success)): ?>
                <div class="alert alert-success alert-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo htmlspecialchars($contact_success); ?>
                </div>
            <?php endif; ?>
            
            <div class="upload-section">
                <div class="section-title">
                    <i class="bi bi-camera-fill"></i>
                    <span>Profile Picture</span>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="profile_image_upload" class="custom-file-upload">
                            <i class="bi bi-cloud-upload me-2"></i>
                            <span id="file-name-display">Choose Profile Image (JPG, JPEG, PNG)</span>
                        </label>
                        <input type="file" name="profile_image" id="profile_image_upload" class="hidden-file-input" required 
                            onchange="document.getElementById('file-name-display').textContent = this.files.length > 0 ? this.files[0].name : 'Choose Profile Image (JPG, JPEG, PNG)'">
                    </div>
                    <button type="submit" class="btn btn-custom btn-primary-custom w-100">
                        <i class="bi bi-upload me-2"></i>Update Profile Picture
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
                            <?php echo htmlspecialchars($full_name); ?>
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['email']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['Phone_Num']); ?></div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title">
                    <i class="bi bi-book-half"></i>
                    <span>Academic Information</span>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-card-text"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Matriculation Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['Matric_No']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Program / Department</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['Department']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-layers-half"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Level</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['Level']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-gender-ambiguous"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['Gender']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Account Creation Date</div>
                        <div class="info-value"><?php echo htmlspecialchars($formatted_date); ?></div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="../student_modules/student_dashboard.php" class="btn btn-custom btn-secondary-custom">
                    <i class="bi bi-arrow-left-circle me-2"></i>Back to Dashboard
                </a>
                <a href="../student_modules/student_edit_contact.php" 
                class="btn edit-contact-btn">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Contact Info
                </a>
                <a href="../student_modules/student_change_password.php" class="btn btn-custom btn-warning-custom">
                    <i class="bi bi-key-fill me-2"></i>Change Password
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>