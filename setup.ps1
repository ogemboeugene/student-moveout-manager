# Student Move-Out Manager - PowerShell Setup Script

Write-Host "Student Move-Out Manager - Windows Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if MySQL is available
try {
    $mysqlVersion = mysql --version 2>&1
    Write-Host "MySQL found: $mysqlVersion" -ForegroundColor Green
} catch {
    Write-Host "ERROR: MySQL is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install MySQL and add it to your PATH" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""

# Get MySQL credentials
$mysqlUser = Read-Host "Enter MySQL username (default: root)"
if ([string]::IsNullOrEmpty($mysqlUser)) {
    $mysqlUser = "root"
}

$mysqlPassword = Read-Host "Enter MySQL password" -AsSecureString
$mysqlPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($mysqlPassword))

Write-Host ""
Write-Host "Creating database..." -ForegroundColor Yellow

# Create database
$createDbQuery = "CREATE DATABASE IF NOT EXISTS student_moveout_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
try {
    echo $createDbQuery | mysql -u $mysqlUser -p$mysqlPasswordPlain
    Write-Host "Database created successfully!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Failed to create database" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""
Write-Host "Importing schema..." -ForegroundColor Yellow

# Import schema
try {
    mysql -u $mysqlUser -p$mysqlPasswordPlain student_moveout_manager < "database\schema.sql"
    Write-Host "Schema imported successfully!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Failed to import schema" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""
Write-Host "Importing sample data..." -ForegroundColor Yellow

# Import sample data
try {
    mysql -u $mysqlUser -p$mysqlPasswordPlain student_moveout_manager < "database\sample-data.sql"
    Write-Host "Sample data imported successfully!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Failed to import sample data" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""
Write-Host "Setting up configuration..." -ForegroundColor Yellow

# Setup configuration
if (!(Test-Path "config\config.php")) {
    Copy-Item "config\config.example.php" "config\config.php"
    Write-Host "Configuration file created from example." -ForegroundColor Green
    Write-Host "Please edit config\config.php with your database credentials." -ForegroundColor Yellow
} else {
    Write-Host "Configuration file already exists." -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Setup completed successfully!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Default login credentials:" -ForegroundColor Yellow
Write-Host "  Admin: admin / admin123" -ForegroundColor White
Write-Host "  Teacher: teacher1 / teacher123" -ForegroundColor White
Write-Host ""
Write-Host "To start the application:" -ForegroundColor Yellow
Write-Host "  1. Start your web server (Apache/Nginx/XAMPP)" -ForegroundColor White
Write-Host "  2. Navigate to this project directory in your browser" -ForegroundColor White
Write-Host "  3. Login with the credentials above" -ForegroundColor White
Write-Host ""
Write-Host "Display screen (public): /display/" -ForegroundColor Cyan
Write-Host ""

Read-Host "Press Enter to exit"
