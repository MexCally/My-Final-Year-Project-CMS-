<?php
require_once 'config/db.php';

echo "<h2>Comprehensive Status Fix</h2>";

// Fix all registrations with comprehensive mapping
$fixes = [
    // Fix approved registrations
    ["UPDATE course_regtbl SET approval_status = 'Registered' WHERE approved_by IS NOT NULL AND date_approved IS NOT NULL", "approved registrations"],
    // Fix pending registrations
    ["UPDATE course_regtbl SET approval_status = 'Pending' WHERE approval_status = 'pending' OR approval_status = '' OR approval_status IS NULL", "pending registrations"],
    // Fix declined registrations
    ["UPDATE course_regtbl SET approval_status = 'Dropped' WHERE approval_status = 'declined'", "declined registrations"]
];

$totalFixed = 0;
foreach ($fixes as $fix) {
    $updateStmt = $pdo->prepare($fix[0]);
    $result = $updateStmt->execute();
    
    if ($result) {
        $rowsAffected = $updateStmt->rowCount();
        $totalFixed += $rowsAffected;
        echo "<p>✅ Fixed $rowsAffected {$fix[1]}</p>";
    } else {
        echo "<p>❌ Failed to fix {$fix[1]}</p>";
    }
}

echo "<p><strong>Total registrations fixed: $totalFixed</strong></p>";

// Show final status summary
echo "<h3>Final Status Summary:</h3>";
$statusStmt = $pdo->prepare("SELECT approval_status, COUNT(*) as count FROM course_regtbl GROUP BY approval_status");
$statusStmt->execute();
$statuses = $statusStmt->fetchAll();

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Status</th><th>Count</th></tr>";
foreach ($statuses as $status) {
    echo "<tr><td>" . htmlspecialchars($status['approval_status'] ?? 'NULL') . "</td><td>" . $status['count'] . "</td></tr>";
}
echo "</table>";
?>