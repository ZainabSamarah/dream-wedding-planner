# MySQL Won't Start - Complete Fix Guide

## Problem
MySQL keeps stopping immediately after starting due to **InnoDB log file corruption**.

---

## ✅ **SOLUTION 1: Delete InnoDB Log Files (RECOMMENDED)**

### **Option A: Use the Script I Created**
1. **Stop MySQL** in XAMPP Control Panel (if running)
2. **Right-click** on `fix_mysql_corruption.bat` in your project folder
3. Select **"Run as Administrator"**
4. Follow the prompts
5. Start MySQL in XAMPP Control Panel

### **Option B: Manual Steps**
1. **Stop MySQL** in XAMPP Control Panel
2. Open File Explorer and go to: `C:\xampp\mysql\data`
3. **Delete these files:**
   - `ib_logfile0`
   - `ib_logfile1`
   - `ib_logfile2` (if exists)
4. Go back to XAMPP Control Panel
5. **Start MySQL** - it will recreate the log files automatically ✅

---

## ✅ **SOLUTION 2: Increase Recovery Level**

If Solution 1 doesn't work, try stronger recovery mode:

1. Open XAMPP Control Panel as Administrator
2. Click **Config** → **my.ini** (next to MySQL)
3. Find `[mysqld]` section
4. Change the recovery line to:
   ```ini
   innodb_force_recovery = 4
   ```
5. Save and try starting MySQL
6. If it starts, **immediately backup your database**
7. Then remove the recovery line and restart

---

## ✅ **SOLUTION 3: Fresh Start (Last Resort)**

If nothing works, restore from backup folder:

1. **Stop MySQL**
2. Rename `C:\xampp\mysql\data` to `C:\xampp\mysql\data_OLD`
3. Copy `C:\xampp\mysql\backup` to `C:\xampp\mysql\data`
4. Start MySQL
5. Restore your database from SQL backup (if you have one)

---

---

## ✅ **SOLUTION 4: Fix Aria Engine Corruption (SYSTEM TABLES)**

If you see errors like `Aria engine: Redo phase failed` or `Can't open and lock privilege tables` in your error log, try this:

1. **Stop MySQL** in XAMPP
2. **Right-click** on `fix_aria_corruption.bat` in your project folder
3. Select **"Run as Administrator"**
4. Start MySQL in XAMPP

This repairs the MariaDB system tables and clears the corrupted Aria logs.

---

## 🎯 **RECOMMENDED: Try Solution 1 or 4 First**

The easiest fix for normal crashes is Solution 1. If that fails, Solution 4 usually handles the more serious "system table" corruption.
