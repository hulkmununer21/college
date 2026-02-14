# Step 3: Academic Structure Module - COMPLETED! 🎉

## What's New in Step 3

The complete academic structure management system has been implemented, providing a foundation for organizing the entire institution's academic framework.

## 📁 New Files Created

### Database
- ✅ `database/migrations/002_create_academic_structure.sql` - Complete schema with seed data

### Models (5 files)
- ✅ `app/models/Faculty.php` - Faculty management model
- ✅ `app/models/Department.php` - Department management model
- ✅ `app/models/Level.php` - Academic level model
- ✅ `app/models/Session.php` - Academic session model
- ✅ `app/models/Semester.php` - Semester management model

### Controllers
- ✅ `app/controllers/AcademicStructureController.php` - Complete CRUD operations for all components

### Views - Academic Structure (18 files)
- ✅ `app/views/academic/index.php` - Academic structure overview dashboard
- ✅ `app/views/academic/faculties/index.php` - Faculty list
- ✅ `app/views/academic/faculties/create.php` - Create faculty form
- ✅ `app/views/academic/faculties/edit.php` - Edit faculty form
- ✅ `app/views/academic/faculties/view.php` - Faculty details
- ✅ `app/views/academic/departments/index.php` - Department list
- ✅ `app/views/academic/departments/create.php` - Create department form
- ✅ `app/views/academic/departments/edit.php` - Edit department form
- ✅ `app/views/academic/departments/view.php` - Department details
- ✅ `app/views/academic/levels/index.php` - Level list
- ✅ `app/views/academic/levels/create.php` - Create level form
- ✅ `app/views/academic/levels/edit.php` - Edit level form
- ✅ `app/views/academic/sessions/index.php` - Session list
- ✅ `app/views/academic/sessions/create.php` - Create session form
- ✅ `app/views/academic/sessions/edit.php` - Edit session form
- ✅ `app/views/academic/sessions/view.php` - Session details with semesters
- ✅ `app/views/academic/semesters/create.php` - Create semester form
- ✅ `app/views/academic/semesters/edit.php` - Edit semester form

### Updates
- ✅ `app/views/layouts/dashboard.php` - Added Academic Structure menu item for Super Admin and HOD

## 📊 Database Structure

### 1. Faculties Table
```sql
- id (Primary Key)
- name (Unique) - e.g., "Faculty of Science"
- code (Unique) - e.g., "SCI"
- description
- dean_id (Foreign Key to users)
- is_active
- created_at, updated_at
```

**Seed Data:** 6 faculties (Science, Engineering, Arts, Social Sciences, Medicine, Law)

### 2. Departments Table
```sql
- id (Primary Key)
- faculty_id (Foreign Key)
- name - e.g., "Computer Science"
- code (Unique) - e.g., "CSC"
- description
- hod_id (Foreign Key to users)
- is_active
- created_at, updated_at
```

**Relationships:**
- Belongs to Faculty
- Has many Students
- Has many Lecturers

**Seed Data:** 18 departments across all faculties

### 3. Levels Table
```sql
- id (Primary Key)
- name - e.g., "100 Level"
- level_number (Unique) - 100, 200, 300, 400, etc.
- description
- min_credit_units (Default: 15)
- max_credit_units (Default: 24)
- is_active
- created_at, updated_at
```

**Seed Data:** 6 levels (100, 200, 300, 400, 500, 600)

### 4. Sessions Table (Academic Years)
```sql
- id (Primary Key)
- name (Unique) - e.g., "2025/2026"
- start_year, end_year
- start_date, end_date
- is_current (Only one can be current)
- is_active
- created_at, updated_at
```

**Seed Data:** 3 sessions (2024/2025, 2025/2026, 2026/2027)

### 5. Semesters Table
```sql
- id (Primary Key)
- session_id (Foreign Key)
- name - "First Semester" or "Second Semester"
- semester_number (1 or 2)
- start_date, end_date
- registration_start_date
- registration_end_date
- is_current (Only one can be current)
- is_active
- created_at, updated_at
```

**Unique Constraint:** session_id + semester_number

**Seed Data:** 6 semesters (2 per session)

### 6. Users Table Updates
```sql
Added columns:
- department_id (Foreign Key to departments)
- level_id (Foreign Key to levels)
- matric_number (Unique) - For students
- staff_id (Unique) - For staff
```

## 🔧 Setup Instructions

### 1. Run the Database Migration

```bash
mysql -u root -p school_management_db < database/migrations/002_create_academic_structure.sql
```

**This migration will:**
- Create 5 new tables (faculties, departments, levels, sessions, semesters)
- Update users table with new columns
- Insert seed data (6 faculties, 18 departments, 6 levels, 3 sessions, 6 semesters)
- Create stored procedures for setting current session/semester
- Create database views for easy querying
- Add triggers to ensure only one current session/semester

### 2. Verify the Installation

After running the migration, you should see:
- 6 Faculties
- 18 Departments
- 6 Levels
- 3 Sessions
- 6 Semesters

**Check with:**
```sql
SELECT COUNT(*) FROM faculties;
SELECT COUNT(*) FROM departments;
SELECT COUNT(*) FROM levels;
SELECT COUNT(*) FROM sessions;
SELECT COUNT(*) FROM semesters;
```

### 3. Access Academic Structure Management

**Super Admin:**
- Navigate to: `/academic-structure` or use the sidebar menu "Academic Structure"

**HOD:**
- Navigate to: `/academic-structure` (read-only access for HOD)

## 🎯 Features Implemented

### 1. Faculty Management

**Create Faculty:**
- Faculty name (required, unique)
- Faculty code (2-5 uppercase letters, unique)
- Description (optional)
- Auto-assign Dean (optional)

**Features:**
- View all faculties with statistics
- Edit faculty details
- Toggle active/inactive status
- View faculty details (departments, students, lecturers count)
- Search faculties

**Test:**
1. Go to `/academic-structure/faculties`
2. Click "Create Faculty"
3. Fill form: Name: "Faculty of Computing", Code: "COMP"
4. View created faculty with department count

### 2. Department Management

**Create Department:**
- Select parent faculty (required)
- Department name (required, unique within faculty)
- Department code (2-5 uppercase letters, unique globally)
- Description (optional)
- Auto-assign HOD (optional)

**Features:**
- View all departments with faculty info
- Filter by faculty
- Edit department details
- Toggle active/inactive status
- View department details (students, lecturers, statistics)
- Assign HOD to department

**Test:**
1. Go to `/academic-structure/departments`
2. Click "Create Department"
3. Select faculty, enter name and code
4. View department with student/lecturer count

### 3. Level Management

**Create Level:**
- Level name (e.g., "100 Level")
- Level number (100, 200, 300, etc.)
- Minimum credit units per semester (default: 15)
- Maximum credit units per semester (default: 24)
- Description (optional)

**Features:**
- View all levels with student count
- Edit level details
- Update credit unit limits
- Toggle active/inactive status
- Credit unit validation for course registration

**Test:**
1. Go to `/academic-structure/levels`
2. View pre-configured levels (100-600)
3. Edit any level to change credit limits
4. New level registration will enforce these limits

### 4. Session Management (Academic Years)

**Create Session:**
- Session name (e.g., "2026/2027")
- Start year and end year (must be consecutive)
- Start date and end date
- Auto-validation to prevent overlapping sessions

**Features:**
- View all sessions with semester count
- Set current session (only one can be current)
- Edit session details
- View session with all semesters
- Create semesters within session
- Session date overlap validation

**Test:**
1. Go to `/academic-structure/sessions`
2. See current session highlighted
3. Click "Create Session"
4. Fill form: "2027/2028", dates: Sep 1, 2027 - Aug 31, 2028
5. Click "Set as Current" to activate

### 5. Semester Management

**Create Semester:**
- Select parent session
- Semester number (1 or 2)
- Semester name ("First Semester" or "Second Semester")
- Semester start/end dates
- Registration start/end dates
- All dates must be within session period

**Features:**
- View semesters within each session
- Set current semester (automatically sets parent session as current)
- Edit semester details
- Registration period management
- Check if registration is open
- Semester date validation

**Test:**
1. Go to a session view
2. Click "Add Semester"
3. Select "First Semester" (auto-fills name)
4. Set dates within session period
5. Set registration period (starts before semester)
6. Click "Set as Current" to activate

## 🔍 Database Views & Procedures

### Views Created

**v_departments_full:**
Gets complete department info with faculty, HOD, student/lecturer counts

**v_current_semester:**
Gets current semester with session info and registration status

**v_users_complete:** (Optional)
Can be added to get user info with department, level, and role details

### Stored Procedures

**sp_set_current_session(session_id):**
- Sets specified session as current
- Unsets all other sessions
- Unsets all semesters

**sp_set_current_semester(semester_id):**
- Sets specified semester as current
- Unsets all other semesters
- Sets parent session as current

**Usage:**
```sql
CALL sp_set_current_session(2);  -- Set 2025/2026 as current
CALL sp_set_current_semester(4); -- Set Second Semester 2025/2026 as current
```

### Triggers

**before_session_update:**
Ensures only one session is marked as current

**before_semester_update:**
Ensures only one semester is marked as current

## 📝 URL Routes Available

### Main Dashboard
- `/academic-structure` - Academic structure overview

### Faculty Routes
- `/academic-structure/faculties` - List all faculties
- `/academic-structure/create-faculty` - Create faculty form
- `/academic-structure/edit-faculty/{id}` - Edit faculty
- `/academic-structure/view-faculty/{id}` - Faculty details
- `/academic-structure/toggle-faculty/{id}` - Toggle active status
- `POST /academic-structure/store-faculty` - Store new faculty
- `POST /academic-structure/update-faculty/{id}` - Update faculty

### Department Routes
- `/academic-structure/departments` - List all departments
- `/academic-structure/create-department` - Create department form
- `/academic-structure/edit-department/{id}` - Edit department
- `/academic-structure/view-department/{id}` - Department details
- `/academic-structure/toggle-department/{id}` - Toggle active status
- `POST /academic-structure/store-department` - Store new department
- `POST /academic-structure/update-department/{id}` - Update department

### Level Routes
- `/academic-structure/levels` - List all levels
- `/academic-structure/create-level` - Create level form
- `/academic-structure/edit-level/{id}` - Edit level
- `/academic-structure/toggle-level/{id}` - Toggle active status
- `POST /academic-structure/store-level` - Store new level
- `POST /academic-structure/update-level/{id}` - Update level

### Session Routes
- `/academic-structure/sessions` - List all sessions
- `/academic-structure/create-session` - Create session form
- `/academic-structure/edit-session/{id}` - Edit session
- `/academic-structure/view-session/{id}` - Session with semesters
- `/academic-structure/set-current-session/{id}` - Set as current
- `POST /academic-structure/store-session` - Store new session
- `POST /academic-structure/update-session/{id}` - Update session

### Semester Routes
- `/academic-structure/create-semester/{session_id}` - Create semester form
- `/academic-structure/edit-semester/{id}` - Edit semester
- `/academic-structure/set-current-semester/{id}` - Set as current
- `POST /academic-structure/store-semester` - Store new semester
- `POST /academic-structure/update-semester/{id}` - Update semester

## 🔒 Security & Validation

### Access Control
- **Super Admin:** Full CRUD access to all academic structure
- **HOD:** Read-only access to academic structure (can be extended)

### Form Validation

**Faculty:**
- Name: Required, unique
- Code: Required, 2-5 uppercase letters, unique

**Department:**
- Faculty: Required, must exist
- Name: Required, unique within faculty
- Code: Required, 2-5 uppercase letters, unique globally

**Level:**
- Name: Required
- Level Number: Required, must be unique
- Min Credits: Must be >= 0
- Max Credits: Must be > Min Credits

**Session:**
- Name: Required, unique
- Years: End year must be start year + 1
- Dates: End date must be after start date
- No overlapping with existing sessions

**Semester:**
- Session: Required, must exist
- Number: 1 or 2, unique within session
- Dates: Must be within session dates
- Registration: Must start on/after semester start

### CSRF Protection
All forms include CSRF tokens for security

## 🧪 Testing Checklist

### Faculty Management
- [ ] Create new faculty
- [ ] Edit faculty details
- [ ] View faculty with departments
- [ ] Toggle faculty active/inactive
- [ ] Search faculties
- [ ] Verify code uniqueness validation
- [ ] Verify name uniqueness validation

### Department Management
- [ ] Create department under faculty
- [ ] Edit department details
- [ ] Change department faculty
- [ ] View department with students/lecturers
- [ ] Toggle department active/inactive
- [ ] Verify code uniqueness globally
- [ ] Verify name uniqueness within faculty

### Level Management
- [ ] Create new level
- [ ] Edit level credit limits
- [ ] Toggle level active/inactive
- [ ] Verify level number uniqueness
- [ ] Verify min/max credit validation

### Session Management
- [ ] Create new session
- [ ] Edit session dates
- [ ] Set session as current
- [ ] View session with semesters
- [ ] Verify date overlap prevention
- [ ] Verify only one current session

### Semester Management
- [ ] Create first semester
- [ ] Create second semester
- [ ] Edit semester dates
- [ ] Set semester as current
- [ ] Verify dates within session
- [ ] Verify registration period validation
- [ ] Check registration status

### Integration Testing
- [ ] Create complete structure: Faculty → Department → Level
- [ ] Create session and add both semesters
- [ ] Set current session and semester
- [ ] Verify dashboard shows current info
- [ ] Switch current semester
- [ ] Deactivate entities and verify cascade

## 📊 Sample Data Structure

After running the migration, your database will have:

### Faculties (6)
1. Faculty of Science (SCI)
2. Faculty of Engineering (ENG)
3. Faculty of Arts (ART)
4. Faculty of Social Sciences (SOC)
5. Faculty of Medicine (MED)
6. Faculty of Law (LAW)

### Departments (18)
**Science:** Computer Science, Mathematics, Physics, Chemistry, Biology
**Engineering:** Electrical, Mechanical, Civil, Chemical
**Arts:** English, History, Philosophy
**Social Sciences:** Economics, Business Administration, Political Science
**Medicine:** Medicine & Surgery, Nursing
**Law:** Law

### Levels (6)
- 100 Level (15-24 credits)
- 200 Level (15-24 credits)
- 300 Level (15-24 credits)
- 400 Level (15-24 credits)
- 500 Level (15-24 credits)
- 600 Level (15-24 credits)

### Sessions (3)
- 2024/2025 (Inactive)
- 2025/2026 (Current)
- 2026/2027 (Upcoming)

### Semesters (6)
- 2 semesters for each session
- Second Semester 2025/2026 is current (matches today's date: Feb 2026)

## 💡 Usage Examples

### Example 1: Setting Up a New Department

```php
// In AcademicStructureController
$departmentId = $this->departmentModel->insert([
    'faculty_id' => 1,  // Faculty of Science
    'name' => 'Software Engineering',
    'code' => 'SWE',
    'description' => 'Department of Software Engineering'
]);
```

### Example 2: Getting Current Semester Info

```php
// In any controller
$semesterModel = $this->model('Semester');
$currentSemester = $semesterModel->getCurrentSemesterWithSession();

// Returns:
[
    'semester_name' => 'Second Semester',
    'session_name' => '2025/2026',
    'is_registration_open' => 1,
    'registration_end_date' => '2026-02-15'
]
```

### Example 3: Validating Credit Units

```php
// In course registration
$levelModel = $this->model('Level');
$validation = $levelModel->validateCreditUnits($studentLevelId, $totalCredits);

if (!$validation['valid']) {
    echo $validation['message'];
    // "Maximum credit units for 100 Level is 24"
}
```

### Example 4: Getting Department Students

```php
$departmentModel = $this->model('Department');
$students = $departmentModel->getStudents($departmentId, $levelId);

// Returns array of students with matric numbers, names, levels
```

## 🚀 What's Next: Step 4 Preview

In the next module, we'll build **Course Management**:
- Course creation with credit units
- Course prerequisites tracking
- Course assignment to departments/levels
- Lecturer assignment to courses
- Student course registration
- Credit unit validation per level
- Course registration workflow
- Registration approval process

## 💡 Tips & Best Practices

### 1. Academic Calendar Setup
Always set up your academic structure in this order:
1. Create Faculties
2. Create Departments under faculties
3. Create/verify Levels (usually pre-configured)
4. Create new Session for upcoming year
5. Create Semesters for the session
6. Set current session and semester

### 2. Managing Current Session
- Only one session can be "current" at a time
- Setting a semester as current automatically sets its parent session as current
- Before starting a new session, ensure the previous one is completed

### 3. Registration Periods
- Registration start date should be before semester start
- Allow at least 2-4 weeks for course registration
- Late registration period can be handled separately

### 4. Credit Unit Limits
- Different departments may have different limits
- Medicine typically has higher credit limits
- Consider allowing admin to override in special cases

### 5. Data Integrity
- Don't delete faculties/departments with existing students
- Use "inactive" status instead of deletion
- Changing a student's department requires approval workflow

## 📞 Troubleshooting

### Issue: Migration fails on foreign key constraints
**Solution:** Ensure you ran Step 2 migration first. Users table must exist.

### Issue: Can't set session as current
**Solution:** Check if another session is already current. Only one can be current.

### Issue: Department code already exists
**Solution:** Department codes must be unique globally, not just within faculty.

### Issue: Semester dates validation fails
**Solution:** Ensure all semester dates are within the parent session's date range.

### Issue: Academic Structure menu not showing
**Solution:** 
1. Clear browser cache
2. Verify you're logged in as Super Admin or HOD
3. Check [dashboard.php](app/views/layouts/dashboard.php) was updated correctly

### Issue: Can't create semester with same number
**Solution:** Each session can only have two semesters (1 and 2). Delete or edit existing one first.

## 📚 Database Relationships

```
faculties (1) ──→ (many) departments
faculties (1) ──→ (1) users (dean)
departments (1) ──→ (1) users (hod)
departments (1) ──→ (many) users (students/lecturers)
levels (1) ──→ (many) users (students)
sessions (1) ──→ (many) semesters
```

## 🎓 Learning Points

From this module, you've learned:
1. ✅ Complex database relationships and foreign keys
2. ✅ Data validation at multiple levels (database, model, controller)
3. ✅ Using database views for complex queries
4. ✅ Stored procedures for business logic
5. ✅ Triggers for data integrity
6. ✅ Managing hierarchical data (Faculty → Department)
7. ✅ Handling "current" status with database constraints
8. ✅ Seed data management for initial setup
9. ✅ Building admin interfaces for data management
10. ✅ Implementing search and filter functionality

---

**Congratulations!** 🎉 Step 3 is complete! You now have a fully functional academic structure management system. The foundation is set for course management, student registration, and result computation in the upcoming modules.

Ready to build the Course Management Module in Step 4? Let me know! 📚
