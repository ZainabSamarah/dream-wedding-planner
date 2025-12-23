# Fix XAMPP MySQL Startup Issue

## Problem Detected
Your MySQL error log shows **InnoDB corruption** with log sequence number errors. This prevents MySQL from starting.

## Solution: Enable InnoDB Recovery Mode

### Step 1: Edit MySQL Configuration

1. **Open XAMPP Control Panel** as Administrator
2. Click **Config** button next to MySQL
3. Select **my.ini** from the dropdown menu
4. Find the `[mysqld]` section
5. Add this line under `[mysqld]`:
   ```ini
   innodb_force_recovery = 1
   ```

### Step 2: Start MySQL

1. Save the `my.ini` file
2. In XAMPP Control Panel, click **Start** for MySQL
3. MySQL should now start successfully

### Step 3: Backup Your Database

Once MySQL starts, **immediately backup your database**:

```bash
# Open Command Prompt in XAMPP directory
cd C:\xampp\mysql\bin
mysqldump -u root wedding_db > C:\xampp\htdocs\dream-wedding-planner1\wedding_db_backup.sql
```

### Step 4: Remove Recovery Mode

1. Open `my.ini` again
2. **Remove or comment out** the line:
   ```ini
   # innodb_force_recovery = 1
   ```
3. Restart MySQL

---

## Alternative: Quick Fix Script

If the above doesn't work, try increasing the recovery level (1-6):

- **Level 1**: Let InnoDB run even if corrupt pages are detected
- **Level 2**: Prevent master thread operations
- **Level 3**: Don't run transaction rollbacks
- **Level 4**: Don't calculate table statistics
- **Level 5**: Don't look at undo logs
- **Level 6**: Don't do transaction rollback (use only as last resort)

Start with level 1, and only increase if needed.

---

## If Recovery Fails: Fresh Start

If recovery mode doesn't work, you may need to restore from backup:

1. **Stop MySQL** (if running)
2. **Backup current data folder**:
   - Rename `C:\xampp\mysql\data` to `C:\xampp\mysql\data_old`
3. **Copy fresh data folder**:
   - Copy `C:\xampp\mysql\backup` to `C:\xampp\mysql\data`
4. **Start MySQL**
5. **Restore your database** from SQL backup if you have one

---

## Prevention Tips

- Always use **Stop** button in XAMPP (don't force close)
- Don't shut down computer while MySQL is running
- Regular database backups
