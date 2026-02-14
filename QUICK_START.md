# 🚀 Quick Start - Testing in GitHub Codespaces

## ⚡ Fast Track (5 Minutes)

### 1️⃣ Setup Database (Choose One Method)

#### Option A: Using Railway (Fastest)
```bash
# 1. Go to https://railway.app (free tier available)
# 2. Create new project → Add MySQL
# 3. Copy connection details
```

#### Option B: Using PlanetScale
```bash
# 1. Go to https://planetscale.com (free tier)
# 2. Create new database: school_management_db
# 3. Get connection string
```

#### Option C: Using DigitalOcean
```bash
# 1. Create managed MySQL database
# 2. Note down credentials
```

---

### 2️⃣ Run Automated Setup

```bash
# Navigate to project root
cd /workspaces/college

# Run setup script (interactive)
./setup.sh
```

**The script will:**
- ✅ Check PHP and extensions
- ✅ Test database connection
- ✅ Update configuration
- ✅ Import database schema
- ✅ Start development server

---

### 3️⃣ Manual Setup (Alternative)

#### Import Database Schema

```bash
# Method 1: Using MySQL client
mysql -h YOUR_HOST -u YOUR_USER -p YOUR_DB < database/complete_schema.sql

# Method 2: Using web interface (phpMyAdmin, etc.)
# - Upload: database/complete_schema.sql
# - Execute
```

#### Update Configuration

Edit `app/config/config.php`:

```php
define('DB_HOST', 'your-host.example.com');
define('DB_NAME', 'school_management_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

#### Start Server

```bash
php -S 0.0.0.0:8000 -t public
```

---

### 4️⃣ Access Application

1. Look for **Ports** tab in Codespaces
2. Find port **8000**
3. Click 🌐 (globe icon) to open in browser
4. Or click notification: "Your application is available"

**URL format:** `https://[codespace-name].githubpreview.dev`

---

### 5️⃣ Login & Test

#### Default Admin Login
- **URL:** `/auth/login`
- **Username:** `superadmin`
- **Password:** `Admin@2026`

> ⚠️ **Change this password immediately in production!**

---

## 🧪 Quick Test Scenarios

### Test 1: Create Users (2 mins)
```
1. Login as admin
2. Add Lecturer: lecturer1 / Lecturer@2026
3. Add Student: student1 / Student@2026 (Matric: CSC/2025/001)
4. Add HOD: hod1 / Hod@2026
```

### Test 2: Course Assignment (1 min)
```
1. Go to: Course Management → Courses
2. Find: CSC101
3. Click: Assign Lecturer → Select lecturer1
```

### Test 3: Course Registration (2 mins)
```
1. Logout → Login as student1
2. Go to: Course Registration
3. Select: CSC101, MTH101, PHY101
4. Click: Register for Selected Courses
```

### Test 4: Approve Registration (1 min)
```
1. Logout → Login as hod1 or superadmin
2. Go to: Registration Approvals
3. Select pending registrations
4. Click: Approve Selected
```

### Test 5: Grade Entry (2 mins)
```
1. Logout → Login as lecturer1
2. Go to: Grade Entry → CSC101
3. For each student, enter:
   - CA Score: 35 (out of 40)
   - Exam Score: 55 (out of 60)
   - Click: Save
4. Click: Submit for Approval
```

### Test 6: Approve Grades (1 min)
```
1. Logout → Login as hod1
2. Go to: Grade Approvals → Review CSC101
3. Review statistics
4. Click: Approve All Grades
```

### Test 7: View Results (1 min)
```
1. Logout → Login as student1
2. Go to: My Results
3. View: CGPA, Credits, Class of Degree
4. Click: View Full Transcript
```

**Total Test Time: ~10 minutes** ⏱️

---

## 🔍 Verification Commands

### Check Database Connection
```bash
php test-db-connection.php
```

### Verify Tables Created
```bash
mysql -h HOST -u USER -p -e "USE school_management_db; SHOW TABLES;"
```

### Count Records
```bash
mysql -h HOST -u USER -p -e "
USE school_management_db;
SELECT 'Roles' as table_name, COUNT(*) as count FROM roles
UNION SELECT 'Users', COUNT(*) FROM users
UNION SELECT 'Faculties', COUNT(*) FROM faculties
UNION SELECT 'Departments', COUNT(*) FROM departments
UNION SELECT 'Courses', COUNT(*) FROM courses;
"
```

---

## 📊 Expected Database Counts (After Schema Import)

| Table | Count |
|-------|-------|
| roles | 6 |
| users | 1 (admin) |
| faculties | 6 |
| departments | 18 |
| levels | 6 |
| sessions | 3 |
| semesters | 6 |
| courses | 9 (sample) |

---

## 🛠️ Troubleshooting Quick Fixes

### Can't connect to database
```bash
# Test connection
ping YOUR_DATABASE_HOST

# Check credentials
cat app/config/config.php | grep "DB_"
```

### Tables not found
```bash
# Re-import schema
mysql -h HOST -u USER -p DB < database/complete_schema.sql
```

### Port 8000 not accessible
```bash
# Check if port is in use
lsof -i :8000

# Kill existing process
kill -9 $(lsof -t -i :8000)

# Restart server
php -S 0.0.0.0:8000 -t public
```

### Session errors
```bash
# Create session directory
mkdir -p /tmp/php_sessions
chmod 777 /tmp/php_sessions
```

---

## 📚 File Reference

| File | Purpose |
|------|---------|
| `database/complete_schema.sql` | Complete DB schema (all tables) |
| `app/config/config.php` | Configuration file (DB credentials) |
| `setup.sh` | Automated setup script |
| `TESTING_GUIDE.md` | Comprehensive testing documentation |
| `STEP5_GUIDE.md` | Result management documentation |

---

## 🎯 What's Included (Steps 1-5)

✅ **Authentication Module**
- Multi-role login system
- Password reset
- Email verification
- Session management

✅ **Academic Structure**
- Faculties, Departments
- Levels, Sessions, Semesters
- HOD assignments

✅ **Course Management**
- Course creation
- Prerequisites
- Lecturer assignments
- Student registration
- Approval workflow

✅ **Result Management**
- Grade entry (CA + Exam)
- 5.0 grading scale
- GPA/CGPA computation
- Grade approval workflow
- Transcript generation
- Statistics & analytics

---

## 💡 Pro Tips

1. **Keep Credentials Safe:** Use environment variables in production
2. **Backup Database:** Export schema regularly during testing
3. **Monitor Logs:** Check browser console for JavaScript errors
4. **Clear Cache:** Clear browser cache if UI doesn't update
5. **Test Incrementally:** Test each module before moving to next

---

## 📞 Need Help?

1. Read: `TESTING_GUIDE.md` (detailed instructions)
2. Read: `STEP5_GUIDE.md` (result management details)
3. Check: Database connection with `test-db-connection.php`
4. Verify: PHP version and extensions
5. Review: Browser console for errors

---

## ✅ Ready for Production?

Before deploying:
- [ ] Change default admin password
- [ ] Set up SSL/HTTPS
- [ ] Configure email settings
- [ ] Set `error_reporting = 0` in config
- [ ] Implement database backups
- [ ] Review security settings
- [ ] Load test application
- [ ] Set up monitoring

---

**Last Updated:** February 14, 2026  
**Environment:** GitHub Codespaces  
**PHP Version:** 8.2+  
**MySQL Version:** 5.7+

---

🎉 **Happy Testing!**
