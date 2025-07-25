# Student Move-Out Manager

A secure, web-based PHP + MySQL application to manage student move-out processes for schools.

## Features

- **Dean (Admin)**: Complete CRUD operations for student records, user management
- **Teacher**: Search and call students with timestamp logging
- **Public Display**: Real-time display of called students with auto-expiry

## Tech Stack

- PHP 8+
- MySQL 8+
- Bootstrap 5 for responsive UI
- Vanilla JavaScript for real-time updates
- bcrypt for password hashing
- CSRF protection

## Installation & Deployment

### Local Development (XAMPP/LAMP)

1. **Database Setup**
   ```sql
   -- Import the database schema
   mysql -u root -p < database/schema.sql
   ```

2. **Configuration**
   ```bash
   cp config/config.example.php config/config.php
   # Edit config.php with your database credentials
   ```

3. **Web Server**
   - Point your web server to the project root
   - Ensure PHP 8+ is installed
   - Enable mod_rewrite for Apache

### Cloud Deployment

#### Option 1: Render + Aiven MySQL (Recommended)
- **Hosting**: Render.com (Docker-based)
- **Database**: Aiven MySQL (managed)
- **Guide**: See `RENDER-DEPLOYMENT.md` for complete step-by-step instructions

#### Option 2: Azure App Service + Azure Database for MySQL
- **Hosting**: Azure App Service
- **Database**: Azure Database for MySQL
- **Guide**: See `HOSTING-GUIDE.md` for Azure deployment

### Default Admin Account
- Username: `admin`
- Password: `admin123`
- **Change this immediately after first login!**

## Directory Structure

```
/
├── config/           # Configuration files
├── database/         # SQL schema and migrations
├── includes/         # Core PHP classes and utilities
├── assets/          # CSS, JS, images
├── admin/           # Dean/Admin panel
├── teacher/         # Teacher panel
├── display/         # Public display screen
└── auth/            # Authentication system
```

## Security Features

- Password hashing with bcrypt
- CSRF token protection
- SQL injection prevention with prepared statements
- Session management with timeouts
- Role-based access control
- Input sanitization and output escaping

## Usage

1. **Admin Panel** (`/admin/`)
   - Manage students (add, edit, delete)
   - View all students
   - Manage user accounts

2. **Teacher Panel** (`/teacher/`)
   - Search students by name or grade
   - Call students (logs timestamp)
   - View call history

3. **Display Screen** (`/display/`)
   - Public display of called students
   - Auto-refresh every 10 seconds
   - Students auto-expire after 5 minutes

## API Endpoints

- `GET /api/called-students.php` - Get currently called students
- `POST /api/call-student.php` - Call a student
- `POST /api/cleanup-expired.php` - Remove expired calls

## License

MIT License
