@echo off
echo Student Move-Out Manager - Windows Setup
echo ========================================
echo.

echo Checking if MySQL is running...
mysql --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: MySQL is not installed or not in PATH
    echo Please install MySQL and add it to your PATH
    pause
    exit /b 1
)

echo MySQL found!
echo.

echo Creating database...
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS student_moveout_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %errorlevel% neq 0 (
    echo ERROR: Failed to create database
    pause
    exit /b 1
)

echo Database created successfully!
echo.

echo Importing schema...
mysql -u root -p student_moveout_manager < database\schema.sql

if %errorlevel% neq 0 (
    echo ERROR: Failed to import schema
    pause
    exit /b 1
)

echo Schema imported successfully!
echo.

echo Importing sample data...
mysql -u root -p student_moveout_manager < database\sample-data.sql

if %errorlevel% neq 0 (
    echo ERROR: Failed to import sample data
    pause
    exit /b 1
)

echo Sample data imported successfully!
echo.

echo Setting up configuration...
if not exist config\config.php (
    copy config\config.example.php config\config.php
    echo Configuration file created from example.
    echo Please edit config\config.php with your database credentials.
) else (
    echo Configuration file already exists.
)

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Default login credentials:
echo   Admin: admin / admin123
echo   Teacher: teacher1 / teacher123
echo.
echo To start the application:
echo   1. Start your web server (Apache/Nginx)
echo   2. Navigate to this project directory in your browser
echo   3. Login with the credentials above
echo.
echo Display screen (public): /display/
echo.
pause
