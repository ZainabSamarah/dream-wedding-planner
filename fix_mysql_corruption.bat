@echo off
echo ========================================
echo MySQL InnoDB Corruption Fix Script
echo ========================================
echo.
echo This script will:
echo 1. Stop MySQL (if running)
echo 2. Delete corrupted InnoDB log files
echo 3. MySQL will recreate them on next start
echo.
echo WARNING: Make sure XAMPP Control Panel is open as Administrator!
echo.
pause

echo.
echo Step 1: Stopping MySQL...
cd C:\xampp
mysql_stop.bat
timeout /t 3

echo.
echo Step 2: Deleting corrupted InnoDB log files...
cd C:\xampp\mysql\data

if exist ib_logfile0 (
    echo Deleting ib_logfile0...
    del /F ib_logfile0
)

if exist ib_logfile1 (
    echo Deleting ib_logfile1...
    del /F ib_logfile1
)

if exist ib_logfile2 (
    echo Deleting ib_logfile2...
    del /F ib_logfile2
)

echo.
echo Step 3: Log files deleted successfully!
echo.
echo ========================================
echo NEXT STEPS:
echo ========================================
echo 1. Go to XAMPP Control Panel
echo 2. Click START for MySQL
echo 3. MySQL should now start successfully!
echo.
echo The InnoDB log files will be recreated automatically.
echo ========================================
echo.
pause
