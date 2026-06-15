UNIVERSITY TEACHER PORTAL - VPS DEPLOYMENT

Default Login:
Email: admin@university.local
Password: Admin@12345

Main Features:
- Admin/teacher/student login
- Create student IDs and passwords
- Create assignment, quiz, homework and viva tasks
- Upload teacher question files
- Student downloads and secure controlled file access
- Countdown due timer for students
- Student answer upload
- Teacher submission download
- MySQL backup/import SQL included

VPS Requirements:
- Ubuntu VPS or cPanel hosting
- Apache or Nginx
- PHP 8.1+
- MySQL/MariaDB
- php-mysql extension

Ubuntu VPS Install Steps:
1. Upload university_teacher_portal.zip to /var/www/html/
2. Unzip:
   sudo apt update
   sudo apt install apache2 mysql-server php php-mysql unzip -y
   cd /var/www/html
   sudo unzip university_teacher_portal.zip
   sudo chown -R www-data:www-data university_teacher_portal
   sudo chmod -R 755 university_teacher_portal
   sudo chmod -R 775 university_teacher_portal/uploads

3. Create database:
   sudo mysql
   SOURCE /var/www/html/university_teacher_portal/sql/database.sql;
   EXIT;

4. Edit config.php:
   sudo nano /var/www/html/university_teacher_portal/config.php
   Set db_name, db_user, db_pass.

5. Open browser:
   http://YOUR_SERVER_IP/university_teacher_portal/

Security Notes Before Production:
- Change default admin password after first login by updating database or adding a profile page.
- Use HTTPS/SSL.
- Move uploads outside public web root for stronger security.
- Increase PHP upload limits if files are larger than 20 MB.
- Take regular MySQL backups.
