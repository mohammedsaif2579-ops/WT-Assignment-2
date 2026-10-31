CAMPUSLY STUDENT RECORDS ADMIN PANEL

REQUIREMENTS
- XAMPP (Apache and MySQL)
- PHP 8.0 or later recommended
- MySQL with PDO MySQL and Fileinfo extensions enabled

SETUP
1. Extract this folder into C:\xampp\htdocs\.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open http://localhost/phpmyadmin/ in your browser.
4. Import database.sql. It creates the campusly_college database and tables.
5. Check config.php. The default XAMPP settings are host=localhost, user=root, blank password.
6. Open http://localhost/campusly_student_admin_assignment2/

FEATURES
- Add, view, edit, delete, and search student records.
- Upload profile images (JPG, PNG, WEBP; maximum 2 MB).
- Upload documents (PDF, DOC, DOCX; maximum 5 MB).
- Save file metadata and upload date in MySQL.
- Display files on the student detail page.
- Detect MIME type, use unique stored filenames, and validate file size.
- Prepared SQL statements, HTML output escaping, and CSRF form tokens.

NOTES
- This is a PHP/MySQL project; Java Servlet WEB-INF files are not used.
- The uploads folders contain .htaccess files that block PHP execution on Apache.
- For a production deployment, store uploads outside the public web root and configure access control.
