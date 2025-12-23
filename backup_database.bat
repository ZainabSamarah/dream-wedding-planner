@echo off
echo Creating database backup...
cd C:\xampp\mysql\bin
mysqldump -u root wedding_db > C:\xampp\htdocs\dream-wedding-planner1\wedding_db_backup.sql
echo Backup complete! File saved to: wedding_db_backup.sql
pause
