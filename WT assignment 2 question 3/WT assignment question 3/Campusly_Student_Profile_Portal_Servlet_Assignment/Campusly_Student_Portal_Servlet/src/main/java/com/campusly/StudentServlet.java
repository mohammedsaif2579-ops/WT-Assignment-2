package com.campusly;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/students")
public class StudentServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        List<Student> students = new ArrayList<>();

        String sql = "SELECT s.id, s.name, s.roll_number, s.email, s.course, s.phone, "
                   + "f.id AS file_id, f.original_name, f.file_type "
                   + "FROM students s LEFT JOIN student_files f ON s.id = f.student_id "
                   + "ORDER BY s.name, f.uploaded_at DESC";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {

            Student current = null;
            int previousId = -1;

            // The LEFT JOIN returns one row per file; group those rows into student objects.
            while (rs.next()) {
                int studentId = rs.getInt("id");

                if (studentId != previousId) {
                    current = new Student();
                    current.setId(studentId);
                    current.setName(rs.getString("name"));
                    current.setRollNumber(rs.getString("roll_number"));
                    current.setEmail(rs.getString("email"));
                    current.setCourse(rs.getString("course"));
                    current.setPhone(rs.getString("phone"));
                    students.add(current);
                    previousId = studentId;
                }

                // A student can have no files, one file, or several files.
                if (rs.getObject("file_id") != null) {
                    StudentFile file = new StudentFile();
                    file.setId(rs.getInt("file_id"));
                    file.setOriginalName(rs.getString("original_name"));
                    file.setFileType(rs.getString("file_type"));
                    current.getFiles().add(file);
                }
            }
        } catch (Exception e) {
            throw new ServletException("Could not load student records.", e);
        }

        request.setAttribute("students", students);
        request.getRequestDispatcher("/students.jsp").forward(request, response);
    }
}
