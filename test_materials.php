<?php
// Simple test to check if the lecturer materials endpoint works
session_start();

// Simulate lecturer session for testing
$_SESSION['lecturer_id'] = 2; // Using lecturer ID 2 from the database

// Include the materials endpoint
include 'PHP/get_lecturer_course_materials.php';
?>