<?php
session_start();
header('Content-Type: application/json');
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$role = null;
$ok = false;
if (isset($_SESSION['admin_id'])) {
    $ok = true;
    $role = 'admin';
} elseif (isset($_SESSION['lecturer_id'])) {
    $ok = true;
    $role = 'lecturer';
} elseif (isset($_SESSION['student_id'])) {
    $ok = true;
    $role = 'student';
}

echo json_encode(['ok' => $ok, 'user_role' => $role]);
exit();
