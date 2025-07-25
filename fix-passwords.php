<?php
/**
 * Fix Password Hashes Script
 * This script will generate proper password hashes and update the database
 */

require_once 'includes/init.php';

echo "Fixing password hashes...\n\n";

try {
    $db = Database::getInstance();
    
    // Generate proper hashes
    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $teacherHash = password_hash('teacher123', PASSWORD_DEFAULT);
    
    echo "Generated hashes:\n";
    echo "admin123 hash: " . $adminHash . "\n";
    echo "teacher123 hash: " . $teacherHash . "\n\n";
    
    // Update admin password
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
    $result1 = $stmt->execute([$adminHash]);
    
    // Update all teacher passwords
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE role = 'teacher'");
    $result2 = $stmt->execute([$teacherHash]);
    
    if ($result1 && $result2) {
        echo "✅ Password hashes updated successfully!\n\n";
        echo "You can now login with:\n";
        echo "Admin: admin / admin123\n";
        echo "Teachers: teacher1, teacher2, teacher3 / teacher123\n\n";
        
        // Test the hashes
        echo "Testing password verification:\n";
        echo "admin123 verification: " . (password_verify('admin123', $adminHash) ? "✅ PASS" : "❌ FAIL") . "\n";
        echo "teacher123 verification: " . (password_verify('teacher123', $teacherHash) ? "✅ PASS" : "❌ FAIL") . "\n";
        
    } else {
        echo "❌ Error updating password hashes\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🔧 Password fix complete. You can now delete this file.\n";
?>
