# 🧪 Testing Guide for GitHub Codespaces

## 📋 Table of Contents
1. [Environment Setup](#environment-setup)
2. [Database Configuration](#database-configuration)
3. [Running the Application](#running-the-application)
4. [Testing Workflows](#testing-workflows)
5. [Sample Test Data](#sample-test-data)
6. [Troubleshooting](#troubleshooting)

---

## 🔧 Environment Setup

### Prerequisites
- GitHub Codespaces (already running)
- Remote MySQL database (e.g., AWS RDS, DigitalOcean, PlanetScale, or any MySQL host)
- Database credentials (host, username, password, database name)

### Step 1: Verify PHP Installation

```bash
php --version
# Should show PHP 8.2.x or higher
```

### Step 2: Check Required Extensions

```bash
php -m | grep -E "pdo|pdo_mysql|mbstring|json|openssl"
```

All these extensions should be listed. If any are missing:

```bash
sudo apt-get update
sudo apt-get install -y php-mysql php-mbstring
```

---

## 🗄️ Database Configuration

### Option 1: Remote Database (Recommended for Testing)

#### Popular Remote Database Providers

**A. PlanetScale (Free Tier Available)**
- URL: https://planetscale.com
- Provides: Serverless MySQL database
- Free tier: 5GB storage, 1 billion row reads/month

**B. AWS RDS MySQL**
- URL: https://aws.amazon.com/rds/mysql/
- Provides: Managed MySQL database
- Free tier: 750 hours/month for 12 months

**C. DigitalOcean Managed Database**
- URL: https://www.digitalocean.com/products/managed-databases-mysql
- Provides: Managed MySQL cluster

**D. Railway**
- URL: https://railway.app
- Provides: MySQL database with free tier
- Easy one-click deployment

### Step 1: Import Complete Database Schema

Once you have your remote database credentials:

#### Method 1: Using MySQL Client (from terminal)

```bash
# Install MySQL client if not present
sudo apt-get install -y mysql-client

# Import the complete schema
mysql -h YOUR_HOST -u YOUR_USERNAME -p YOUR_DATABASE < database/complete_schema.sql

# Example:
# mysql -h db.example.com -u myuser -p school_db < database/complete_schema.sql
```

#### Method 2: Using phpMyAdmin or Database GUI
1. Access your database management interface
2. Create a new database named `school_management_db`
3. Import the file: `database/complete_schema.sql`

#### Method 3: Copy-Paste SQL (for web interfaces)
1. Open `database/complete_schema.sql` in the terminal:
   ```bash
   cat database/complete_schema.sql
   ```
2. Copy the entire content
3. Paste into your database SQL query interface
4. Execute

### Step 2: Update Configuration File

Edit the file: `app/config/config.php`

```bash
nano app/config/config.php
# or
code app/config/config.php
```

Update these lines with your remote database credentials:

```php
// Database Configuration
define('DB_HOST', 'your-remote-host.example.com');  // e.g., mysql-123.railway.app
define('DB_NAME', 'school_management_db');           // Your database name
define('DB_USER', 'your_username');                  // Your database username
define('DB_PASS', 'your_password');                  // Your database password
define('DB_CHARSET', 'utf8mb4');
```

**Security Note:** For production, use environment variables instead of hardcoding credentials.

### Step 3: Test Database Connection

Create a test file:

```bash
cat > test-db-connection.php << 'EOF'
<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful!\n";
    
    // Test query
    $stmt = $db->query("SELECT COUNT(*) as count FROM roles");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Roles table found: " . $result['count'] . " roles\n";
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Users table found: " . $result['count'] . " users\n";
    
    echo "\n🎉 Database is ready for testing!\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

php test-db-connection.php
```

Expected output:
```
✅ Database connection successful!
✅ Roles table found: 6 roles
✅ Users table found: 1 users

🎉 Database is ready for testing!
```

---

## 🚀 Running the Application

### Option 1: Using PHP Built-in Server

```bash
# Navigate to the public directory
cd /workspaces/college

# Start PHP server on port 8000
php -S 0.0.0.0:8000 -t public
```

Output should show:
```
[Fri Feb 14 10:00:00 2026] PHP 8.2.x Development Server started
```

### Access the Application

In GitHub Codespaces:
1. A notification will appear: "Your application running on port 8000 is available"
2. Click **"Open in Browser"**
3. Or go to the **Ports** tab and click the globe icon next to port 8000

The URL will be something like: `https://hulkmununer21-college-xxxxx.githubpreview.dev`

### Option 2: Using Apache (if installed)

```bash
# Check if Apache is installed
sudo systemctl status apache2

# If not installed
sudo apt-get install -y apache2

# Configure virtual host
sudo nano /etc/apache2/sites-available/school.conf
```

Add this configuration:
```apache
<VirtualHost *:80>
    DocumentRoot /workspaces/college/public
    
    <Directory /workspaces/college/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/school_error.log
    CustomLog ${APACHE_LOG_DIR}/school_access.log combined
</VirtualHost>
```

Enable and restart:
```bash
sudo a2ensite school.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 🧪 Testing Workflows

### 1. Login with Default Admin

**URL:** `/auth/login`

**Credentials:**
- Username: `superadmin`
- Password: `Admin@2026`

**Expected:** Redirect to Admin Dashboard

---

### 2. Create Test Users

#### Create a Lecturer

1. Login as admin
2. Navigate to **Users** → **Add User**
3. Fill in:
   - **Username:** `lecturer1`
   - **Email:** `lecturer@school.edu`
   - **Password:** `Lecturer@2026`
   - **First Name:** `John`
   - **Last Name:** `Doe`
   - **Role:** Lecturer
   - **Department:** Computer Science
4. Click **Create User**

#### Create a Student

1. Navigate to **Users** → **Add User**
2. Fill in:
   - **Username:** `student1`
   - **Email:** `student@school.edu`
   - **Password:** `Student@2026`
   - **First Name:** `Jane`
   - **Last Name:** `Smith`
   - **Role:** Student
   - **Department:** Computer Science
   - **Level:** 100 Level
   - **Matric Number:** `CSC/2025/001`
3. Click **Create User**

#### Create an HOD

1. Navigate to **Users** → **Add User**
2. Fill in:
   - **Username:** `hod1`
   - **Email:** `hod@school.edu`
   - **Password:** `Hod@2026`
   - **First Name:** `Mary`
   - **Last Name:** `Johnson`
   - **Role:** Head of Department
   - **Department:** Computer Science
4. Click **Create User**

---

### 3. Test Course Registration Workflow

#### Step 1: Assign Lecturer to Course (as Admin/HOD)

1. Login as admin/HOD
2. Navigate to **Course Management** → **Courses**
3. Click **Assign Lecturer** on a course (e.g., CSC101)
4. Select `lecturer1` (John Doe)
5. Select current session
6. Click **Assign**

#### Step 2: Register for Course (as Student)

1. **Logout** and login as `student1`
2. Navigate to **Course Registration**
3. You should see available courses (CSC101, MTH101, PHY101, etc.)
4. Select **CSC101**, **MTH101**, **PHY101**
5. Click **Register for Selected Courses**
6. Status should be **Pending**

#### Step 3: Approve Registration (as HOD/Admin)

1. **Logout** and login as `hod1` or `superadmin`
2. Navigate to **Registration Approvals**
3. You should see pending registrations from `student1`
4. Select registrations
5. Click **Approve Selected**
6. Status changes to **Approved**

---

### 4. Test Grade Entry Workflow

#### Step 1: Enter Grades (as Lecturer)

1. **Logout** and login as `lecturer1`
2. Navigate to **Grade Entry**
3. Click on **CSC101** course
4. You should see registered students
5. For `Jane Smith (CSC/2025/001)`:
   - **CA Score:** 35 (out of 40)
   - **Exam Score:** 55 (out of 60)
   - **Total:** 90 (auto-calculated)
   - **Grade:** A (auto-calculated)
   - **Grade Point:** 5.0 (auto-calculated)
6. Click **Save** for the student
7. After entering all grades, click **Submit for Approval**

#### Step 2: Approve Grades (as HOD/Admin)

1. **Logout** and login as `hod1` or `superadmin`
2. Navigate to **Grade Approvals**
3. You should see pending grades for CSC101
4. Click **Review & Approve**
5. Review statistics (average score, pass rate, distribution)
6. Select all grades
7. Click **Approve All Grades**

#### Step 3: View Results (as Student)

1. **Logout** and login as `student1`
2. Navigate to **My Results**
3. You should see:
   - **CGPA:** 5.00 (since only one course with grade A)
   - **Total Credits:** 3
   - **Class of Degree:** First Class Honours
4. Click **View Full Transcript**
5. Transcript should display all results

---

### 5. Test Statistics & Analytics

1. Login as `lecturer1` or `superadmin`
2. Navigate to a course with approved grades
3. Click **Statistics**
4. Verify:
   - Average score
   - Pass rate
   - Grade distribution chart
   - Highest/Lowest scores

---

## 📊 Sample Test Data (SQL Scripts)

### Quick Test Data Setup

Run this SQL to create complete test data:

```sql
USE school_management_db;

-- Create test lecturer
INSERT INTO users (role_id, username, email, password_hash, first_name, last_name, department_id, is_active, is_email_verified) VALUES
(5, 'lecturer1', 'lecturer@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', 1, 1, 1);

-- Create test HOD
INSERT INTO users (role_id, username, email, password_hash, first_name, last_name, department_id, is_active, is_email_verified) VALUES
(4, 'hod1', 'hod@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary', 'Johnson', 1, 1, 1);

-- Create test students
INSERT INTO users (role_id, username, email, password_hash, first_name, last_name, department_id, level_id, matric_number, is_active, is_email_verified) VALUES
(6, 'student1', 'student1@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', 1, 1, 'CSC/2025/001', 1, 1),
(6, 'student2', 'student2@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Williams', 1, 1, 'CSC/2025/002', 1, 1),
(6, 'student3', 'student3@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Brown', 1, 1, 'CSC/2025/003', 1, 1);

-- Assign lecturer to courses
SET @lecturer_id = (SELECT id FROM users WHERE username = 'lecturer1');
SET @current_session_id = (SELECT id FROM sessions WHERE is_current = 1);

INSERT INTO course_lecturers (course_id, lecturer_id, session_id, is_coordinator) VALUES
((SELECT id FROM courses WHERE code = 'CSC101'), @lecturer_id, @current_session_id, 1),
((SELECT id FROM courses WHERE code = 'CSC102'), @lecturer_id, @current_session_id, 0);

-- Register students for courses
SET @student1_id = (SELECT id FROM users WHERE username = 'student1');
SET @student2_id = (SELECT id FROM users WHERE username = 'student2');
SET @student3_id = (SELECT id FROM users WHERE username = 'student3');
SET @current_semester_id = (SELECT id FROM semesters WHERE is_current = 1);
SET @csc101_id = (SELECT id FROM courses WHERE code = 'CSC101');
SET @mth101_id = (SELECT id FROM courses WHERE code = 'MTH101');

INSERT INTO course_registrations (student_id, course_id, semester_id, status, approved_by, approved_at) VALUES
(@student1_id, @csc101_id, @current_semester_id, 'approved', 1, NOW()),
(@student1_id, @mth101_id, @current_semester_id, 'approved', 1, NOW()),
(@student2_id, @csc101_id, @current_semester_id, 'approved', 1, NOW()),
(@student3_id, @csc101_id, @current_semester_id, 'approved', 1, NOW());

SELECT '✅ Test data created successfully!' AS message;
SELECT 'Test Credentials:' AS info;
SELECT 'Admin: superadmin / Admin@2026' AS credentials;
SELECT 'Lecturer: lecturer1 / Lecturer@2026' AS credentials;
SELECT 'HOD: hod1 / Hod@2026' AS credentials;
SELECT 'Student: student1 / Student@2026' AS credentials;
```

**Apply test data:**

```bash
mysql -h YOUR_HOST -u YOUR_USERNAME -p YOUR_DATABASE < test-data.sql
```

---

## 🐛 Troubleshooting

### Issue 1: "Connection refused" or "Can't connect to MySQL"

**Solution:**
- Verify database host and port
- Check firewall rules (ensure port 3306 is open)
- Test connection with MySQL client:
  ```bash
  mysql -h YOUR_HOST -u YOUR_USERNAME -p
  ```

### Issue 2: "Access denied for user"

**Solution:**
- Verify username and password in `config.php`
- Ensure the database user has proper permissions:
  ```sql
  GRANT ALL PRIVILEGES ON school_management_db.* TO 'your_user'@'%';
  FLUSH PRIVILEGES;
  ```

### Issue 3: "Table doesn't exist"

**Solution:**
- Re-import the schema:
  ```bash
  mysql -h YOUR_HOST -u YOUR_USERNAME -p YOUR_DATABASE < database/complete_schema.sql
  ```

### Issue 4: "404 Not Found" or Routes not working

**Solution:**
- Ensure `.htaccess` exists in `public/`:
  ```bash
  ls -la public/.htaccess
  ```
- If missing, create it:
  ```bash
  cat > public/.htaccess << 'EOF'
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php [QSA,L]
  EOF
  ```

### Issue 5: Session not persisting

**Solution:**
- Check PHP session directory:
  ```bash
  php -i | grep "session.save_path"
  ```
- Create directory if needed:
  ```bash
  mkdir -p /tmp/php_sessions
  chmod 777 /tmp/php_sessions
  ```

### Issue 6: "CSRF token mismatch"

**Solution:**
- Clear browser cookies
- Logout and login again
- Check session configuration in `config.php`

---

## 📝 Testing Checklist

Use this checklist to verify all functionality:

### Authentication Module
- [ ] Login with admin credentials
- [ ] Login with wrong credentials (should fail)
- [ ] Logout functionality
- [ ] Password reset request
- [ ] Account lockout after 5 failed attempts
- [ ] Remember me functionality

### User Management
- [ ] Create new user (each role)
- [ ] Edit user details
- [ ] Deactivate user
- [ ] View user list with pagination

### Academic Structure
- [ ] Create faculty
- [ ] Create department
- [ ] Assign HOD to department
- [ ] View academic structure

### Course Management
- [ ] Create course
- [ ] Assign lecturer to course
- [ ] Set course prerequisites
- [ ] Student course registration
- [ ] Registration approval workflow

### Result Management
- [ ] Lecturer enters grades
- [ ] Submit grades for approval
- [ ] HOD approves grades
- [ ] Student views results
- [ ] Generate transcript
- [ ] View course statistics
- [ ] View top performers

### Security
- [ ] CSRF protection works
- [ ] SQL injection prevention (try `admin' OR '1'='1`)
- [ ] XSS prevention (try `<script>alert('XSS')</script>`)
- [ ] Role-based access control (student can't access admin pages)

---

## 🎯 Performance Testing

### Test Database Performance

```bash
# Create a script to test query performance
cat > test-performance.php << 'EOF'
<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Database.php';

$db = Database::getInstance()->getConnection();

// Test 1: Simple query
$start = microtime(true);
$stmt = $db->query("SELECT * FROM users LIMIT 100");
$results = $stmt->fetchAll();
$time1 = microtime(true) - $start;
echo "✅ Query 1 (SELECT users): " . round($time1 * 1000, 2) . "ms\n";

// Test 2: Complex join
$start = microtime(true);
$stmt = $db->query("
    SELECT u.*, d.name as dept_name, l.name as level_name
    FROM users u
    LEFT JOIN departments d ON u.department_id = d.id
    LEFT JOIN levels l ON u.level_id = l.id
    LIMIT 100
");
$results = $stmt->fetchAll();
$time2 = microtime(true) - $start;
echo "✅ Query 2 (JOIN users+depts+levels): " . round($time2 * 1000, 2) . "ms\n";

// Test 3: Aggregate query
$start = microtime(true);
$stmt = $db->query("
    SELECT 
        d.name,
        COUNT(*) as student_count
    FROM users u
    JOIN departments d ON u.department_id = d.id
    WHERE u.role_id = (SELECT id FROM roles WHERE code = 'STUDENT')
    GROUP BY d.id
");
$results = $stmt->fetchAll();
$time3 = microtime(true) - $start;
echo "✅ Query 3 (Aggregate students by dept): " . round($time3 * 1000, 2) . "ms\n";

echo "\n📊 All queries should complete in < 100ms for good performance.\n";
EOF

php test-performance.php
```

---

## 🔄 Continuous Testing

### Auto-reload during development

```bash
# Install nodemon (requires Node.js)
npm install -g nodemon

# Use nodemon to auto-restart PHP server on file changes
nodemon --exec "php -S 0.0.0.0:8000 -t public" --watch app --ext php
```

---

## 📞 Support

If you encounter issues:

1. Check the **Troubleshooting** section above
2. Review error logs:
   ```bash
   tail -f /var/log/apache2/error.log  # If using Apache
   # or check PHP errors in browser (ensure error reporting is on)
   ```
3. Verify database connection and schema
4. Clear browser cache and cookies

---

## ✅ Next Steps After Testing

Once testing is complete:

1. **Change default passwords** (especially admin password!)
2. **Configure email settings** in `config.php` for password reset
3. **Set up backup strategy** for database
4. **Configure SSL/HTTPS** for production
5. **Set error reporting to production mode** (`display_errors = Off`)
6. **Proceed to Step 6: Bursary Module**

---

**Generated:** Testing Guide for Steps 1-5  
**Last Updated:** February 14, 2026  
**Environment:** GitHub Codespaces + Remote MySQL
