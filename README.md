# Tertiary School Management System

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange.svg)](VERSION)

A robust, secure, and scalable Tertiary School Management System built with **Vanilla PHP 8.2+** and **MySQL** using the **MVC (Model-View-Controller)** architectural pattern.

## 📋 Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Core Components](#core-components)
- [Security Features](#security-features)
- [Usage](#usage)
- [Development Roadmap](#development-roadmap)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Multi-Role Authentication System
- **Super Administrator**: Complete system control
- **Admission Officer**: Manage student applications and admissions
- **Bursar**: Financial operations and fee management
- **Head of Department (HOD)**: Department management and oversight
- **Lecturer**: Course management and student assessment
- **Student**: Self-service portal access

### Academic Management
- Faculty and Department structure
- Level and Session management
- Semester organization
- Course creation and management
- Course registration with validation
- Credit unit tracking
- Prerequisites checking

### Result Management
- Grade entry system with validation (CA + Exam)
- Approval workflow (Lecturer → HOD → Student)
- Automated GPA/CGPA computation (Nigerian 5.0 scale)
- Class of Degree classification
- Student results portal
- Academic transcript generation (print-ready)
- Course statistics and analytics
- Top performers leaderboard

### Financial Management
- Fee structure management
- Payment tracking
- Invoice generation
- Payment history
- Bursary operations

## 🔧 System Requirements

- **PHP**: 8.2 or higher
- **MySQL**: 5.7 or higher (8.0+ recommended)
- **Apache/Nginx**: Web server with mod_rewrite enabled
- **Extensions**: PDO, PDO_MySQL, mbstring, json, openssl

## 📥 Installation

### Step 1: Clone or Download

```bash
git clone https://github.com/hulkmununer21/college.git
cd college
```

### Step 2: Configure Web Server

#### Apache Configuration

Create a virtual host or configure your `.htaccess` files (already included).

**Virtual Host Example:**
```apache
<VirtualHost *:80>
    ServerName school.local
    DocumentRoot "/path/to/college/public"
    
    <Directory "/path/to/college/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to your hosts file (`/etc/hosts` or `C:\Windows\System32\drivers\etc\hosts`):
```
127.0.0.1 school.local
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name school.local;
    root /path/to/college/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?url=$uri&$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### Step 3: Configure Database

1. Edit `config/database.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

2. Import the database schema:

```bash
mysql -u root -p < database/schema.sql
```

Or using phpMyAdmin, import the `database/schema.sql` file.

### Step 4: Set Permissions

```bash
chmod -R 755 /path/to/college
chmod -R 775 /path/to/college/public/uploads
```

### Step 5: Update Configuration

Edit `config/config.php` to match your environment:

```php
define('BASE_URL', 'http://school.local');
define('SESSION_SECURE', true); // Set to true in production with HTTPS
```

### Step 6: Access the Application

Open your browser and navigate to:
```
http://school.local
```

**Default Login Credentials:**
- **Username**: `superadmin`
- **Email**: `admin@school.edu`
- **Password**: `Admin@2026`

⚠️ **IMPORTANT**: Change the default password immediately after first login!

## 📁 Project Structure

```
college/
├── app/
│   ├── controllers/         # Application controllers
│   │   ├── HomeController.php
│   │   └── ErrorController.php
│   ├── models/             # Data models
│   │   └── User.php
│   ├── views/              # View templates
│   │   ├── layouts/
│   │   │   └── default.php
│   │   ├── home/
│   │   │   └── index.php
│   │   └── errors/
│   │       └── 404.php
│   └── helpers/            # Helper classes and functions
│
├── core/                   # Core framework classes
│   ├── Database.php        # Singleton database class with PDO
│   ├── Router.php          # URL routing handler
│   ├── Controller.php      # Base controller class
│   └── Model.php           # Base model class
│
├── config/                 # Configuration files
│   ├── config.php          # Application configuration
│   └── database.php        # Database credentials
│
├── public/                 # Publicly accessible directory
│   ├── index.php           # Application entry point
│   ├── .htaccess           # Apache rewrite rules
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   ├── images/             # Image assets
│   └── uploads/            # User uploaded files (gitignored)
│
├── database/               # Database files
│   └── schema.sql          # Database schema
│
├── .htaccess               # Root Apache configuration
├── .gitignore              # Git ignore rules
└── README.md               # This file
```

## ⚙️ Configuration

### Application Settings

Edit `config/config.php` for application-level settings:

- **APP_NAME**: Application name
- **BASE_URL**: Base URL of the application
- **SESSION_LIFETIME**: Session timeout (seconds)
- **CSRF_TOKEN_EXPIRE**: CSRF token expiration (seconds)

### Database Settings

Edit `config/database.php` for database configuration:

- **DB_HOST**: Database host
- **DB_NAME**: Database name
- **DB_USER**: Database username
- **DB_PASS**: Database password

## 🗄️ Database Setup

The database schema includes:

### Core Tables
- **roles**: System roles and permissions
- **users**: User accounts
- **user_sessions**: Active user sessions
- **audit_logs**: System audit trail

### Pre-populated Data
- 6 default roles (Super Admin, Admission Officer, Bursar, HOD, Lecturer, Student)
- 1 Super Admin user account

### Database Features
- Foreign key constraints
- Indexes for performance
- JSON storage for permissions
- Audit triggers
- Cleanup stored procedures
- Materialized views

## 🏗️ Core Components

### 1. Database Class (Singleton Pattern)

```php
$db = Database::getInstance();
$db->query("SELECT * FROM users WHERE id = :id")
   ->bind(':id', $userId)
   ->fetch();
```

**Features:**
- Singleton pattern for single connection
- PDO for prepared statements
- Method chaining support
- Transaction support
- Automatic error handling

### 2. Router Class

Handles clean URLs like: `school.com/controller/method/param1/param2`

**Features:**
- Automatic controller loading
- Method dispatching
- Parameter parsing
- 404 error handling
- RESTful routing support

### 3. Base Controller

Parent class for all controllers with common functionality:

```php
class MyController extends Controller {
    public function index() {
        $this->view('my/view', ['title' => 'Page Title']);
    }
}
```

**Methods:**
- `view()`: Render views with layouts
- `model()`: Load models
- `redirect()`: URL redirection
- `json()`: Return JSON responses
- `requireAuth()`: Authentication check
- `requireRole()`: Role-based access control
- `generateCSRFToken()`: CSRF protection
- `flash()`: Flash messages

### 4. Base Model

Parent class for all models with CRUD operations:

```php
class MyModel extends Model {
    protected string $table = 'my_table';
    protected array $fillable = ['column1', 'column2'];
}
```

**Methods:**
- `find()`: Find by ID
- `findAll()`: Get all records
- `where()`: Query with conditions
- `insert()`: Create new record
- `update()`: Update record
- `delete()`: Delete record
- `count()`: Count records

## 🔒 Security Features

### 1. SQL Injection Prevention
- All queries use PDO prepared statements
- Parameter binding with type checking
- No raw SQL in controllers or views

### 2. CSRF Protection
```php
// Generate token in form
$token = $this->generateCSRFToken();

// Verify token on submission
if ($this->verifyCSRFToken($_POST['csrf_token'])) {
    // Process form
}
```

### 3. Session Security
- Secure session configuration
- Session hijacking prevention
- Automatic session timeout
- Session regeneration on login

### 4. Password Security
- Bcrypt hashing (PASSWORD_BCRYPT)
- Automatic hash generation
- Password strength validation
- Account lockout after failed attempts

### 5. Input Sanitization
```php
$clean = $this->sanitize($_POST['data']);
```

### 6. XSS Prevention
- HTML entity encoding
- Content Security Policy headers
- Output escaping in views

### 7. Access Control
- Role-based permissions
- Route protection
- Method-level authorization

## 🚀 Usage

### Creating a Controller

Create `app/controllers/StudentController.php`:

```php
<?php
class StudentController extends Controller
{
    public function index(): void
    {
        $this->requireRole('STUDENT');
        
        $studentModel = $this->model('Student');
        $students = $studentModel->findAll();
        
        $this->view('student/index', [
            'title' => 'Students',
            'students' => $students
        ]);
    }
}
```

### Creating a Model

Create `app/models/Student.php`:

```php
<?php
class Student extends Model
{
    protected string $table = 'students';
    protected array $fillable = ['first_name', 'last_name', 'email'];
    
    public function findByMatricNumber(string $matricNumber)
    {
        return $this->findWhere(['matric_number' => $matricNumber]);
    }
}
```

### Creating a View

Create `app/views/student/index.php`:

```php
<div class="container">
    <h1><?= htmlspecialchars($title) ?></h1>
    
    <table>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['first_name']) ?></td>
            <td><?= htmlspecialchars($student['last_name']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
```

### URL Structure

- Homepage: `http://school.local/`
- Controller: `http://school.local/student`
- Method: `http://school.local/student/view`
- Parameters: `http://school.local/student/view/123`

## 🗺️ Development Roadmap

### ✅ Step 1: Core Infrastructure (COMPLETED)
- [x] Directory structure
- [x] Database class (Singleton + PDO)
- [x] Router class
- [x] Base Controller and Model
- [x] User authentication schema

### ✅ Step 2: Authentication Module (COMPLETED)
- [x] Login/Logout functionality
- [x] Registration with validation
- [x] Password reset via email
- [x] Email verification
- [x] Session management
- [x] Remember me functionality
- [x] Role-based dashboards
- [x] CSRF protection
- [x] Account lockout after failed attempts

### ✅ Step 3: Academic Structure (COMPLETED)
- [x] Faculty management
- [x] Department management
- [x] Level management
- [x] Session management
- [x] Semester management

### ✅ Step 4: Course Management (COMPLETED)
- [x] Course creation and management
- [x] Course assignment to departments/levels
- [x] Student course registration
- [x] Credit unit validation
- [x] Prerequisites checking
- [x] Registration approval workflow
- [x] Lecturer course assignments

### ✅ Step 5: Result Management (COMPLETED)
- [x] Grade entry interface for lecturers
- [x] GPA calculation (5.0 scale)
- [x] CGPA computation
- [x] Result approval workflow (HOD/Admin)
- [x] Transcript generation
- [x] Student results portal
- [x] Course statistics and analytics
- [x] Top performers leaderboard

### 📋 Step 6: Bursary Module (NEXT)
- [ ] Fee structure management
- [ ] Invoice generation
- [ ] Payment recording
- [ ] Payment verification
- [ ] Payment history
- [ ] Financial reports
- [ ] Revenue analytics

## 🤝 Contributing

This is a learning project. Contributions, issues, and feature requests are welcome!

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

**Senior PHP Backend Developer**

## 🙏 Acknowledgments

- Built with PHP 8.2+ best practices
- Following SOLID principles
- Inspired by modern MVC frameworks
- Security-first approach

---

**Note**: This is Step 1 of the development process. Additional modules will be built incrementally to ensure code quality and maintainability.
