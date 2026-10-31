package com.campusly;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/student-file")
public class StudentFileServlet extends HttpServlet {
    private static final Path STORAGE = Paths.get(
            System.getProperty("catalina.base"), "campusly-storage").toAbsolutePath().normalize();

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        int fileId;
        try {
            fileId = Integer.parseInt(request.getParameter("id"));
        } catch (Exception e) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }

        String sql = "SELECT stored_name, file_type FROM student_files WHERE id = ?";
        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, fileId);

            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) {
                    response.sendError(HttpServletResponse.SC_NOT_FOUND);
                    return;
                }

                // Only the stored server-generated filename is used to locate the file.
                Path file = STORAGE.resolve(rs.getString("stored_name")).normalize();
                if (!file.startsWith(STORAGE) || !Files.isRegularFile(file)) {
                    response.sendError(HttpServletResponse.SC_NOT_FOUND);
                    return;
                }

                response.setContentType(rs.getString("file_type"));
                response.setHeader("X-Content-Type-Options", "nosniff");
                response.setHeader("Content-Disposition", "inline");
                response.setContentLengthLong(Files.size(file));

                try (InputStream in = Files.newInputStream(file);
                     OutputStream out = response.getOutputStream()) {
                    byte[] buffer = new byte[8192];
                    int count;
                    while ((count = in.read(buffer)) != -1) {
                        out.write(buffer, 0, count);
                    }
                }
            }
        } catch (Exception e) {
            throw new ServletException("Unable to retrieve the requested file.", e);
        }
    }
}
