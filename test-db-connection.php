<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

echo "\n";
echo "🔍 Testing Database Connection\n";
echo "================================\n\n";

echo "Configuration:\n";
echo "  Host: " . DB_HOST . "\n";
echo "  Database: " . DB_NAME . "\n";
echo "  User: " . DB_USER . "\n";
echo "  Charset: " . DB_CHARSET . "\n\n";

try {
    // Get database connection
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful!\n\n";
    
    // Test 1: Check roles table
    echo "📋 Checking database tables...\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM roles");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✓ Roles table: " . $result['count'] . " roles found\n";
    
    // Test 2: Check users table
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✓ Users table: " . $result['count'] . " users found\n";
    
    // Test 3: Check faculties table
    $stmt = $db->query("SELECT COUNT(*) as count FROM faculties");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✓ Faculties table: " . $result['count'] . " faculties found\n";
    
    // Test 4: Check departments table
    $stmt = $db->query("SELECT COUNT(*) as count FROM departments");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✓ Departments table: " . $result['count'] . " departments found\n";
    
    // Test 5: Check courses table
    $stmt = $db->query("SELECT COUNT(*) as count FROM courses");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✓ Courses table: " . $result['count'] . " courses found\n";
    
    // Test 6: Check sessions table
    $stmt = $db->query("SELECT COUNT(*) as count FROM sessions");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  ✓ Sessions table: " . $result['count'] . " sessions found\n";
    
    // Test 7: Check current semester
    $stmt = $db->query("SELECT * FROM v_current_semester LIMIT 1");
    $semester = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($semester) {
        echo "  ✓ Current semester: " . $semester['semester_name'] . " (" . $semester['session_name'] . ")\n";
    } else {
        echo "  ⚠ No current semester set\n";
    }
    
    echo "\n🎉 Database is ready for testing!\n";
    echo "\nDefault Login Credentials:\n";
    echo "  Username: superadmin\n";
    echo "  Password: Admin@2026\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "  1. Database credentials in config/database.php\n";
    echo "  2. Database server is accessible\n";
    echo "  3. Database schema has been imported\n";
    echo "\nTo import schema:\n";
    echo "  mysql -h HOST -u USER -p DATABASE < database/complete_schema.sql\n\n";
    exit(1);
}
