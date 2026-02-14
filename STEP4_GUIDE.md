# Step 4: Course Management Module - Implementation Guide

## 📋 Overview

This guide documents the complete implementation of the Course Management Module (Step 4) for the Tertiary School Management System. This module enables course creation, student registration, prerequisite validation, credit unit tracking, and approval workflows.

## 🆕 What's New in Step 4

### Files Created/Modified

#### Database Migration
- `database/migrations/003_create_course_management.sql` - Complete database schema

#### Models (Already Implemented)
- `app/models/Course.php` - Course CRUD and management (532 lines)
- `app/models/CourseRegistration.php` - Registration logic (548 lines)

#### Controllers (Already Implemented)
- `app/controllers/CourseController.php` - Complete course management (786 lines)

#### View Files (Newly Created - 7 files)
1. `app/views/courses/edit.php` - Edit course form
2. `app/views/courses/view.php` - Course details with prerequisites & lecturers
3. `app/views/registration/available.php` - Student course registration interface
4. `app/views/registration/my-registrations.php` - Student's registered courses
5. `app/views/registration/pending.php` - HOD/Admin approval interface
6. `app/views/courses/my-courses.php` - Lecturer's assigned courses
7. `app/views/courses/students.php` - Students enrolled in a course

#### Updated Files
- `app/views/layouts/dashboard.php` - Added course management navigation for all roles

---

## 🗄️ Database Structure

### Tables Created

#### 1. **courses**
Stores course information
```sql
- id (PK)
- code (UNIQUE) - e.g., CSC301
- title - Course name
- description - Course description
- department_id (FK) - Department offering the course
- level_id (FK) - Academic level
- semester_number - 1 or 2
- credit_units - Credit value (1-10)
- is_elective - 0 (Core) or 1 (Elective)
- is_active - Course status
- created_at, updated_at
```

#### 2. **course_prerequisites**
Defines prerequisite relationships
```sql
- id (PK)
- course_id (FK) - Course requiring prerequisite
- prerequisite_course_id (FK) - Required prerequisite course
- created_at
```

#### 3. **course_lecturers**
Assigns lecturers to courses per session
```sql
- id (PK)
- course_id (FK)
- lecturer_id (FK) → users.id
- session_id (FK)
- is_coordinator - Course coordinator flag
- assigned_at
```

#### 4. **course_registrations**
Student course enrollments
```sql
- id (PK)
- student_id (FK) → users.id
- course_id (FK)
- semester_id (FK)
- registration_date
- status - pending | approved | rejected
- approved_by (FK) → users.id
- approved_at
- dropped - Drop status
- dropped_at
```

#### 5. **course_results** (Prepared for Step 5)
Stores student grades
```sql
- id (PK)
- registration_id (FK)
- student_id (FK)
- course_id (FK)
- semester_id (FK)
- ca_score (0-30)
- exam_score (0-70)
- total_score (computed column)
- grade - A, B, C, D, E, F
- grade_point - 5.0, 4.0, 3.0, 2.0, 1.0, 0.0
- status - draft | submitted | approved
- submitted_by, approved_by (FKs)
```

### Views Created

#### v_courses_full
Complete course information with department and level details

#### v_student_registrations
Student registration details with course and semester information

#### v_course_lecturers
Lecturer course assignments with session details

### Stored Procedures

#### sp_get_available_courses(student_id, semester_id)
Returns courses available for a student based on their department, level, and semester

#### sp_calculate_credit_units(student_id, semester_id, OUT total_credits)
Calculates total approved credit units for a student in a semester

#### sp_check_prerequisites(student_id, course_id, OUT prerequisites_met)
Validates if student has completed all required prerequisites

### Seed Data

The migration includes 35+ sample courses for Computer Science department:
- **100 Level**: 10 courses (CSC101-102, MTH101-102, PHY101-102, CHM101-102, GST111-112)
- **200 Level**: 8 courses (CSC201-206, MTH201-202)
- **300 Level**: 8 courses (CSC301-308)
- **400 Level**: 7 courses (CSC401-402, 403-407)

Includes prerequisite relationships (e.g., CSC202 requires CSC201)

---

## 🎯 Features Implemented

### For Super Admin / HOD

#### Course Management
- ✅ View all courses with filtering (department, level, semester, type, status)
- ✅ Create new courses with validation
- ✅ Edit existing courses
- ✅ View course details with statistics
- ✅ Activate/Deactivate courses
- ✅ Manage prerequisites (add/remove)
- ✅ Assign lecturers to courses
- ✅ View course statistics (registrations, approvals, drops)

#### Registration Approvals
- ✅ View pending registrations
- ✅ Approve individual registrations
- ✅ Reject registrations
- ✅ Bulk approve multiple registrations
- ✅ See student credit unit status
- ✅ Filter by department (HOD sees only their department)

### For Students

#### Course Registration
- ✅ View available courses for current semester
- ✅ Register for courses (AJAX-based)
- ✅ View registration status (pending/approved/rejected)
- ✅ Drop pending registrations
- ✅ View registration summary
  - Total courses registered
  - Total credit units
  - Approved vs pending count
  - Credit limit validation

#### Validations
- ✅ Prerequisite checking (cannot register if prerequisites not met)
- ✅ Credit unit limits (min/max per level)
- ✅ Duplicate registration prevention
- ✅ Registration period checking
- ✅ Already registered course detection

### For Lecturers

#### My Courses
- ✅ View assigned courses for current session
- ✅ See course coordinator status
- ✅ View student count per course
- ✅ Access student list for each course
- ✅ Export student list to CSV
- ✅ Print student list

#### Course Students
- ✅ View enrolled students with details
- ✅ Filter by registration status
- ✅ Export to Excel/CSV
- ✅ Print student roster

---

## 🔗 URL Routes Available

### Admin/HOD Course Management
```
GET  /courses                       - View all courses
GET  /courses/create               - Create course form
POST /courses/store                - Store new course
GET  /courses/view/{id}            - View course details
GET  /courses/edit/{id}            - Edit course form
POST /courses/update/{id}          - Update course
POST /courses/toggle/{id}          - Activate/Deactivate course
POST /courses/add-prerequisite/{id}     - Add prerequisite
POST /courses/remove-prerequisite/{id}/{prereqId} - Remove prerequisite
POST /courses/assign-lecturer/{id}      - Assign lecturer
POST /courses/remove-lecturer/{id}/{lecId}/{sessId} - Remove lecturer
```

### Student Course Registration
```
GET  /courses/available            - View available courses
POST /courses/register             - Register for a course (AJAX)
GET  /courses/my-registrations     - View registered courses
POST /courses/drop/{regId}         - Drop a course
```

### Approval Management (HOD/Admin)
```
GET  /courses/pending-approvals    - View pending registrations
POST /courses/approve/{regId}      - Approve registration
POST /courses/reject/{regId}       - Reject registration
POST /courses/bulk-approve         - Bulk approve registrations
```

### Lecturer Views
```
GET  /courses/my-courses           - View assigned courses
GET  /courses/students/{courseId}  - View course students
```

---

## 🔒 Security & Validation

### Access Control

| Route | Allowed Roles |
|-------|--------------|
| Course Management | SUPER_ADMIN, HOD |
| Course Registration | STUDENT |
| Pending Approvals | SUPER_ADMIN, HOD |
| My Courses (Lecturer) | LECTURER |

### Form Validation

#### Course Creation/Update
- **Code**: Required, max 20 chars, unique, auto-uppercase
- **Title**: Required, max 200 chars
- **Department**: Required, valid ID
- **Level**: Required, valid ID
- **Semester**: Required, 1 or 2
- **Credit Units**: Required, 1-10
- **Type**: Core (0) or Elective (1)

#### Course Registration
- Checks if registration is open
- Validates prerequisites are met
- Validates credit unit limits
- Prevents duplicate registrations
- Checks course availability

### CSRF Protection
All POST/PUT/DELETE requests require valid CSRF token

---

## 📊 Database Relationships

```
courses
  ├─→ departments (department_id)
  ├─→ levels (level_id)
  ├─→ course_prerequisites (course_id)
  ├─→ course_lecturers (course_id)
  └─→ course_registrations (course_id)

course_registrations
  ├─→ users (student_id)
  ├─→ courses (course_id)
  ├─→ semesters (semester_id)
  ├─→ users (approved_by)
  └─→ course_results (registration_id)

course_lecturers
  ├─→ courses (course_id)
  ├─→ users (lecturer_id)
  └─→ sessions (session_id)

course_prerequisites
  ├─→ courses (course_id)
  └─→ courses (prerequisite_course_id)
```

---

## 🚀 Setup Instructions

### Step 1: Run Database Migration

```bash
mysql -u root -p school_management_db < database/migrations/003_create_course_management.sql
```

Or via phpMyAdmin: Import `003_create_course_management.sql`

### Step 2: Verify Tables Created

```sql
USE school_management_db;
SHOW TABLES LIKE 'course%';
```

Expected output:
- courses
- course_prerequisites
- course_lecturers
- course_registrations
- course_results

### Step 3: Verify Seed Data

```sql
-- Check courses
SELECT COUNT(*) FROM courses;  -- Should return 35+

-- Check prerequisites
SELECT COUNT(*) FROM course_prerequisites;  -- Should return 7+

-- Check views
SELECT * FROM v_courses_full LIMIT 5;
```

### Step 4: Test Access

1. **Login as Super Admin**
   - Navigate to "Course Management"
   - Verify can view/create/edit courses

2. **Create Test Student** (if not exists)
   - Assign to Computer Science department
   - Assign to 100 Level
   - Login as student

3. **Test Student Registration**
   - Navigate to "Course Registration"
   - Verify can see available courses
   - Register for a course
   - Check "My Courses"

4. **Test Approval (as HOD/Admin)**
   - Navigate to "Registration Approvals"
   - Approve student registration

---

## ✅ Testing Checklist

### Course Management Tests
- [ ] Create a new course (CSC999 - Test Course)
- [ ] Edit course details
- [ ] Add prerequisite to course
- [ ] Remove prerequisite
- [ ] Activate/Deactivate course
- [ ] Filter courses by department
- [ ] Filter courses by level
- [ ] Filter courses by semester

### Student Registration Tests
- [ ] View available courses as student
- [ ] Register for a course
- [ ] Verify registration shows as "Pending"
- [ ] Try registering for same course again (should fail)
- [ ] Drop a pending registration
- [ ] View registration summary (credit units)
- [ ] Try registering when registration closed (should block)

### Prerequisite Validation Tests
- [ ] Register for CSC201 (should succeed - no prerequisites)
- [ ] Register for CSC202 without passing CSC201 (should fail)
- [ ] Mark CSC201 as passed (via database for now)
- [ ] Register for CSC202 again (should succeed)

### Credit Unit Validation Tests
- [ ] Register for multiple courses
- [ ] Verify total credit units calculated correctly
- [ ] Try exceeding maximum credit units for level (should warn)
- [ ] Try registering below minimum units (should warn)

### Approval Workflow Tests
- [ ] Login as HOD
- [ ] View pending registrations
- [ ] Approve individual registration
- [ ] Reject a registration
- [ ] Select multiple registrations
- [ ] Bulk approve selected registrations
- [ ] Verify student sees "Approved" status

### Lecturer Tests
- [ ] Assign lecturer to a course (as Admin)
- [ ] Mark lecturer as coordinator
- [ ] Login as lecturer
- [ ] View "My Courses"
- [ ] Click on a course
- [ ] View student list
- [ ] Export student list to CSV

---

## 🐛 Troubleshooting

### Issue: "No active semester found"
**Solution**: Navigate to Academic Structure → Create a semester for current session and set registration dates

### Issue: "Prerequisites not met" but student passed the course
**Solution**: For now, prerequisites check course_results table. Since Step 5 (Result Management) isn't built yet, you can manually insert test results:

```sql
INSERT INTO course_results (registration_id, student_id, course_id, semester_id, ca_score, exam_score, grade, grade_point, status)
VALUES (1, {student_id}, {prereq_course_id}, {semester_id}, 25, 55, 'B', 4.0, 'approved');
```

### Issue: "Registration closed"
**Solution**: Check semester registration dates:
```sql
SELECT * FROM semesters WHERE is_current = 1;
-- Update registration dates
UPDATE semesters 
SET registration_start_date = NOW(),
    registration_end_date = DATE_ADD(NOW(), INTERVAL 30 DAY)
WHERE is_current = 1;
```

### Issue: "Course code already exists"
**Solution**: Course codes must be unique. Change the code or edit existing course

### Issue: Lecturer cannot see courses
**Solution**: 
1. Verify lecturer is properly assigned to courses (course_lecturers table)
2. Check current session is set
3. Assign lecturer via Admin/HOD → View Course → Assign Lecturer

---

## 📈 Usage Examples

### Example 1: Creating a Course (Admin/HOD)

1. Navigate to **Course Management** → **Add New Course**
2. Fill form:
   - Code: `CSC301`
   - Title: `Database Management Systems`
   - Department: Computer Science
   - Level: 300 Level
   - Semester: First
   - Credit Units: 4
   - Type: Core
3. Click **Create Course**

### Example 2: Adding Prerequisites

1. Navigate to **Course Management** → View CSC301
2. Click **Add Prerequisite**
3. Select `CSC206 - Introduction to Database Systems`
4. Click **Add Prerequisite**
5. Prerequisite now required for CSC301 registration

### Example 3: Student Course Registration

1. Login as student
2. Navigate to **Course Registration**
3. Review available courses for your level/semester
4. Check credit unit summary
5. Click **Register** next to desired course
6. Confirm registration
7. View **My Courses** to see status (Pending)

### Example 4: Approving Registrations (HOD)

1. Login as HOD
2. Navigate to **Registration Approvals**
3. Review pending registrations
4. Check student credit units
5. Select registrations to approve (checkbox)
6. Click **Approve Selected**
7. Confirmation message displayed

### Example 5: Lecturer Viewing Students

1. Login as lecturer
2. Navigate to **My Courses**
3. Click on desired course
4. View **Students** tab
5. See enrolled students with matric numbers
6. Click **Export to Excel** for class list

---

## 💡 Tips & Best Practices

### For Administrators

1. **Set Up Academic Calendar First**
   - Create sessions
   - Create semesters with proper registration dates
   - Set current semester before allowing registrations

2. **Course Planning**
   - Create all courses for a level before allowing registration
   - Set up prerequisites before semester begins
   - Assign lecturers before semester starts

3. **Credit Unit Management**
   - Verify level credit limits are appropriate (Step 3)
   - Typical ranges: 
     - 100/200 Level: 18-24 units
     - 300/400 Level: 15-24 units

4. **Approval Workflow**
   - Set registration deadlines
   - Review registrations daily during reg period
   - Bulk approve after initial verification

### For Students

1. **Register Early**
   - Don't wait until last day of registration
   - Check prerequisites before registering

2. **Credit Units**
   - Monitor your total credit units
   - Core courses are compulsory
   - Choose electives wisely

3. **Course Drops**
   - Can only drop pending registrations
   - Cannot drop approved registrations (policy dependent)

### For HODs

1. **Department Scope**
   - You only see your department's courses and registrations
   - Coordinate with other departments for cross-listed courses

2. **Registration Review**
   - Check prerequisite completion before approval
   - Verify credit unit compliance
   - Look for unusual patterns (too many/few units)

---

## 🔄 Integration with Other Modules

### Step 3: Academic Structure ✅
- Uses `departments`, `levels`, `sessions`, `semesters`
- Registration periods controlled by semester dates
- Credit limits defined in levels table

### Step 5: Result Management 🔜
- Will use `course_results` table
- Grade Point computation (5.0 scale)
- GPA/CGPA calculation
- Transcript generation

### Step 6: Bursary Module 🔜
- Course registration triggers fee invoice
- Payment verification before final approval

---

## 📝 Learning Points

### Key Concepts Demonstrated

1. **Complex Data Relationships**
   - Many-to-many through junction tables
   - Self-referential foreign keys (prerequisites)
   - Computed columns (total_score)

2. **Business Logic Validation**
   - Prerequisite checking with recursive queries
   - Credit unit validation with level limits
   - Registration period enforcement

3. **Workflow Management**
   - Multi-step approval process
   - Status tracking (pending → approved/rejected)
   - Audit trails (who approved, when)

4. **User Experience**
   - AJAX for seamless registration
   - Real-time validation feedback
   - Role-based interfaces
   - Bulk operations for efficiency

5. **Database Views & Procedures**
   - Encapsulating complex queries in views
   - Business logic in stored procedures
   - Performance optimization with indexes

---

## 🚧 Known Limitations & Future Enhancements

### Current Limitations

1. **No Registration Editing**
   - Cannot change course after approval
   - Solution: Add "Course Swap" feature

2. **No Late Registration**
   - Registration closed after deadline
   - Solution: Add "Late Registration" workflow with penalty

3. **Prerequisites Are Binary**
   - Either met or not met, no partial credit
   - Solution: Add "Minimum Grade" requirement (A, B, C, etc)

4. **No Cross-Department Courses**
   - Courses belong to one department
   - Solution: Add `course_departments` junction table

### Planned Enhancements (Step 5)

- Grade entry interface for lecturers
- GPA/CGPA computation
- Result approval workflow
- Transcript generation
- Academic standing calculation (First Class, Second Class, etc)

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

1. **Semester Rollover**
   - Create new semester
   - Set new as current
   - Archive previous semester registrations

2. **Course Updates**
   - Review course offerings each semester
   - Update prerequisites as needed
   - Retire old courses (set inactive)

3. **Data Cleanup**
   - Purge rejected registrations after semester
   - Archive old registration data
   - Monitor database size

### Backup Recommendations

```bash
# Backup course management tables
mysqldump -u root -p school_management_db \
  courses course_prerequisites course_lecturers \
  course_registrations course_results > course_backup_$(date +%Y%m%d).sql
```

---

## ✨ Conclusion

Step 4 (Course Management) is now **COMPLETE**! 

**Delivered:**
- ✅ Complete database schema with 5 tables
- ✅ 3 database views for optimized queries
- ✅ 3 stored procedures for business logic
- ✅ 2 comprehensive models (532 + 548 lines)
- ✅ 1 fully-featured controller (786 lines)
- ✅ 7 polished view files
- ✅ Role-based navigation updates
- ✅ 35+ seed courses with prerequisites

**Next Steps:**
- **Step 5**: Result Management (Grade entry, GPA/CGPA computation, transcripts)
- **Step 6**: Bursary Module (Fee structure, payments, invoices)

---

**Author**: Senior PHP Backend Developer  
**Date**: February 2026  
**Version**: 1.0.0  
**Module**: Course Management (Step 4)
