<?php
// Admin Security Configuration
class AdminSecurity {
    // Secret admin access key - change this to a secure random string
    private static $ADMIN_ACCESS_KEY = "CMS_ADMIN_2025_SECURE_KEY_2005";
    
    // Validate admin access key
    public static function validateAdminKey($provided_key) {
        return hash_equals(self::$ADMIN_ACCESS_KEY, $provided_key);
    }
    
    // Check if user is trying to access admin functions
    public static function requireAdminAccess($provided_key = null) {
        if (!$provided_key || !self::validateAdminKey($provided_key)) {
            http_response_code(403);
            die(json_encode([
                'success' => false,
                'error' => 'Unauthorized: Invalid admin access key'
            ]));
        }
        return true;
    }
    
    // Generate secure admin session
    public static function createAdminSession($admin_id) {
        session_start();
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['user_type'] = 'admin';
        $_SESSION['login_time'] = time();
        $_SESSION['admin_verified'] = true;
    }
    
    // Verify admin session
    public static function verifyAdminSession() {
        session_start();
        return isset($_SESSION['admin_id']) && 
               isset($_SESSION['user_type']) && 
               $_SESSION['user_type'] === 'admin' &&
               isset($_SESSION['admin_verified']) &&
               $_SESSION['admin_verified'] === true;
    }
}
?>