@echo off
setlocal enabledelayedexpansion

echo ============================================================
echo   MySQL Aria + InnoDB Corruption Fix Script
echo ============================================================
echo.
echo This script will:
echo 1. Stop MySQL (if running)
echo 2. Run aria_chk -r on all MariaDB system tables
echo 3. Delete Aria and InnoDB log files
echo.
echo [IMPORTANT] Please ensure XAMPP is NOT running.
echo [IMPORTANT] Run this script as Administrator.
echo.
pause

echo.
echo [Step 1] Stopping MySQL...
cd /d C:\xampp
if exist mysql_stop.bat (
    call mysql_stop.bat
) else (
    taskkill /F /IM mysqld.exe /T 2>nul
)
timeout /t 2 /nobreak >nul

echo.
echo [Step 2] Repairing Aria system tables...
cd /d C:\xampp\mysql\data\mysql
for %%f in (*.MAI) do (
    echo Repairing %%f...
    "C:\xampp\mysql\bin\aria_chk.exe" -r %%f
)

echo.
echo [Step 3] Deleting Aria log files...
cd /d C:\xampp\mysql\data
if exist aria_log.* (
    echo Deleting aria_log files...
    del /F aria_log.*
)

echo.
echo [Step 4] Deleting InnoDB log files...
if exist ib_logfile* (
    echo Deleting ib_logfile files...
    del /F ib_logfile*
)

echo.
echo ============================================================
echo   FIX COMPLETE
echo ============================================================
echo.
echo 1. Open XAMPP Control Panel
echo 2. Click START for MySQL
echo 3. It should now stay running.
echo.
echo If it still fails, check C:\xampp\mysql\data\mysql_error.log
echo ============================================================
echo.
pause
