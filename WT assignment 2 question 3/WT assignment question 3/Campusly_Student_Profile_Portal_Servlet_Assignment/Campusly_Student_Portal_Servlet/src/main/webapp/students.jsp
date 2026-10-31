<%@ page contentType="text/html;charset=UTF-8" %>
<%@ page import="java.util.List,com.campusly.Student,com.campusly.StudentFile" %>
<%
    // The servlet places the student list in this request before forwarding here.
    List<Student> students = (List<Student>) request.getAttribute("students");
    String context = request.getContextPath();
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Profiles | Campusly</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">CAMPUSLY</div>
    <div class="top-right">Signed in as <strong><%= session.getAttribute("username") %></strong>
        <a href="<%= context %>/logout">Sign out</a>
    </div>
</header>
<main class="container">
    <div class="page-heading">
        <div>
            <h1>Student Profiles</h1>
            <p class="muted">View student details and their uploaded documents.</p>
        </div>
        <span class="count"><%= students == null ? 0 : students.size() %> students</span>
    </div>

    <% if (students == null || students.isEmpty()) { %>
        <section class="empty-state">No student records were found.</section>
    <% } else { %>
        <section class="student-grid">
            <% for (Student student : students) { %>
                <article class="student-card">
                    <div class="student-head">
                        <div class="avatar"><%= student.getName().substring(0, 1).toUpperCase() %></div>
                        <div>
                            <h2><%= student.getName() %></h2>
                            <span class="roll"><%= student.getRollNumber() %></span>
                        </div>
                    </div>
                    <dl>
                        <dt>Email</dt><dd><%= student.getEmail() %></dd>
                        <dt>Course</dt><dd><%= student.getCourse() %></dd>
                        <dt>Phone</dt><dd><%= student.getPhone() %></dd>
                    </dl>
                    <div class="files">
                        <h3>Uploaded files</h3>
                        <% if (student.getFiles().isEmpty()) { %>
                            <p class="muted small">No files uploaded for this student.</p>
                        <% } else { %>
                            <ul>
                                <% for (StudentFile file : student.getFiles()) { %>
                                    <li>
                                        <% if (file.getFileType() != null && file.getFileType().startsWith("image/")) { %>
                                            <a class="image-link" href="<%= context %>/student-file?id=<%= file.getId() %>" target="_blank">
                                                <img src="<%= context %>/student-file?id=<%= file.getId() %>"
                                                     alt="Uploaded image for <%= student.getName() %>">
                                                <span><%= file.getOriginalName() %></span>
                                            </a>
                                        <% } else { %>
                                            <a href="<%= context %>/student-file?id=<%= file.getId() %>" target="_blank">
                                                <%= file.getOriginalName() %>
                                            </a>
                                        <% } %>
                                    </li>
                                <% } %>
                            </ul>
                        <% } %>
                    </div>
                </article>
            <% } %>
        </section>
    <% } %>
</main>
</body>
</html>
