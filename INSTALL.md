# Student Move-Out Manager - Installation Guide

## System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 8.0 or higher
- **MySQL**: Version 8.0 or higher
- **Browser**: Modern web browser with JavaScript enabled

## Required PHP Extensions

- `pdo`
- `pdo_mysql`
- `mbstring`
- `openssl`
- `session`

## Installation Steps

### 1. Download/Clone the Project

Download the project files to your web server's document root or a subdirectory.

### 2. Database Setup

#### Option A: Using MySQL Command Line

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE student_moveout_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema
mysql -u root -p student_moveout_manager < database/schema.sql

# Import sample data (optional)
mysql -u root -p student_moveout_manager < database/sample-data.sql
```

#### Option B: Using phpMyAdmin

1. Open phpMyAdmin
2. Create a new database named `student_moveout_manager`
3. Import `database/schema.sql`
4. Import `database/sample-data.sql` (optional)

#### Option C: Using Setup Scripts (Windows)

```cmd
# Run batch file
setup.bat

# Or run PowerShell script
powershell -ExecutionPolicy Bypass -File setup.ps1
```

### 3. Configuration

1. Copy the example configuration:
   ```bash
   cp config/config.example.php config/config.php
   ```

2. Edit `config/config.php` with your database credentials:
   ```php
   'database' => [
       'host' => 'localhost',
       'username' => 'your_mysql_username',
       'password' => 'your_mysql_password',
       'database' => 'student_moveout_manager',
       'charset' => 'utf8mb4'
   ]
   ```

### 4. Web Server Configuration

#### Apache

Ensure `mod_rewrite` is enabled and the `.htaccess` file is in the project root.

#### Nginx

Add this configuration to your server block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}

# Security headers
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

### 5. File Permissions (Linux/macOS)

```bash
# Set proper permissions
chmod 755 /path/to/project
chmod 644 /path/to/project/config/config.php
chmod 644 /path/to/project/.htaccess
```

### 6. Test the Installation

1. Navigate to your project URL in a web browser
2. You should be redirected to the login page
3. Use the default credentials:
   - **Admin**: `admin` / `admin123`
   - **Teacher**: `teacher1` / `teacher123`

## Default User Accounts

| Username  | Password   | Role    | Description |
|-----------|------------|---------|-------------|
| admin     | admin123   | Dean    | Full system access |
| teacher1  | teacher123 | Teacher | Can search and call students |
| teacher2  | teacher123 | Teacher | Can search and call students |
| teacher3  | teacher123 | Teacher | Can search and call students |

**⚠️ IMPORTANT**: Change all default passwords immediately after installation!

## Application URLs

- **Main Application**: `/` (redirects based on role)
- **Admin Panel**: `/admin/`
- **Teacher Panel**: `/teacher/`
- **Display Screen**: `/display/` (public access)
- **Login**: `/auth/login.php`
- **Logout**: `/auth/logout.php`

## API Endpoints

- `GET /api/called-students.php` - Get currently called students
- `POST /api/call-student.php` - Call a student (requires authentication)
- `GET /api/search-students.php` - Search students (requires authentication)
- `GET /api/stats.php` - Get system statistics
- `POST /api/cleanup-expired.php` - Clean up expired calls

## Features

### Admin (Dean) Features

- ✅ Complete student management (CRUD)
- ✅ User account management
- ✅ View call history and statistics
- ✅ System administration

### Teacher Features

- ✅ Search students by name or grade
- ✅ Call students with timestamp logging
- ✅ View personal call history
- ✅ Real-time statistics

### Display Screen Features

- ✅ Public display of called students
- ✅ Auto-refresh every 10 seconds
- ✅ Students auto-expire after 5 minutes
- ✅ Dark mode toggle
- ✅ Responsive design

## Security Features

- ✅ Password hashing with bcrypt
- ✅ CSRF token protection
- ✅ SQL injection prevention
- ✅ Session management
- ✅ Role-based access control
- ✅ Input sanitization
- ✅ Output escaping

## Troubleshooting

### Database Connection Issues

1. Check database credentials in `config/config.php`
2. Ensure MySQL service is running
3. Verify database exists and user has proper permissions

### PHP Errors

1. Check PHP error logs
2. Ensure all required PHP extensions are installed
3. Verify PHP version compatibility (8.0+)

### Permission Issues

```bash
# Fix file permissions (Linux/macOS)
find /path/to/project -type f -exec chmod 644 {} \;
find /path/to/project -type d -exec chmod 755 {} \;
chmod 644 /path/to/project/config/config.php
```

### Web Server Issues

#### Apache
- Ensure `mod_rewrite` is enabled
- Check `.htaccess` file exists and is readable

#### Nginx
- Verify PHP-FPM is running
- Check Nginx configuration syntax

### JavaScript/AJAX Issues

1. Check browser console for errors
2. Ensure JavaScript is enabled
3. Verify CSRF tokens are being sent correctly

## Production Deployment

### Security Checklist

1. ✅ Change all default passwords
2. ✅ Use HTTPS in production
3. ✅ Set strong database passwords
4. ✅ Restrict database access
5. ✅ Enable web server security headers
6. ✅ Keep software updated
7. ✅ Regular backups
8. ✅ Monitor logs

### Performance Optimization

1. Enable PHP OPcache
2. Use MySQL query optimization
3. Enable web server compression
4. Implement proper caching headers
5. Optimize database indexes

### Backup Strategy

```bash
# Database backup
mysqldump -u username -p student_moveout_manager > backup_$(date +%Y%m%d).sql

# File backup
tar -czf project_backup_$(date +%Y%m%d).tar.gz /path/to/project
```

## Support

For issues or questions:

1. Check this installation guide
2. Review the README.md file
3. Check PHP/MySQL error logs
4. Verify all requirements are met

## License

MIT License - See LICENSE file for details.
