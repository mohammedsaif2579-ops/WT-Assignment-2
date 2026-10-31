CAMPUSLY STUDENT PROFILE PORTAL (Servlet + JSP + MySQL)

REQUIREMENTS
- JDK 11 or later
- Apache Tomcat 9
- MySQL 8
- Maven

IMPORTANT
This is a Java Servlet/JSP web application, not a PHP application.
XAMPP's Apache module does not run Java Servlets. Use Tomcat for this project.
You can use the MySQL service from XAMPP.

DATABASE SETUP
1. Start MySQL.
2. Open http://localhost/phpmyadmin/
3. Import database/schema.sql.
4. If MySQL uses a password, update USER and PASSWORD in DBConnection.java.

DEMO LOGIN
Username: admin
Password: admin123

BUILD AND DEPLOY
1. Open a terminal in this project folder.
2. Run: mvn clean package
3. Copy target/campusly-portal.war into Tomcat's webapps folder.
4. Start Tomcat.
5. Open http://localhost:8080/campusly-portal/login

FILE STORAGE
The file streaming servlet reads files from:
<Tomcat installation>/campusly-storage

This directory is outside the web application so files cannot be accessed directly.
The database stores the generated stored_name, MIME type and original file name.
To test image/document display, place a file in the storage directory and insert a
matching row into student_files for a student. The stored_name must match the filename.

ASSIGNMENT FEATURES
- JDBC connection to MySQL
- Servlet fetches student records and associated file metadata
- JSP loops through students and their files
- Conditional display for images, documents and students with no files
- Image <img> and document links route through StudentFileServlet
- Login session, protected routes, session timeout and logout
- Inline comments explain the main operations

SECURITY NOTE
This is a teaching project. The sample SHA-256 password check is kept simple for
classroom demonstration. A real deployment should use salted password hashing
such as BCrypt or Argon2, HTTPS, CSRF protection, and stricter upload validation.
