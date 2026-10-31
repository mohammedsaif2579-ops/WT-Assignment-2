package com.campusly;

import java.io.IOException;
import java.security.MessageDigest;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

@WebServlet("/login")
public class LoginServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        request.getRequestDispatcher("/login.jsp").forward(request, response);
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String username = request.getParameter("username");
        String password = request.getParameter("password");

        String sql = "SELECT id, password_hash FROM users WHERE username = ?";
        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, username);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next() && MessageDigest.isEqual(
                        hash(password).getBytes(), rs.getString("password_hash").getBytes())) {
                    HttpSession session = request.getSession(true);
                    session.setAttribute("userId", rs.getInt("id"));
                    session.setAttribute("username", username);
                    session.setMaxInactiveInterval(20 * 60);
                    response.sendRedirect(request.getContextPath() + "/students");
                    return;
                }
            }
        } catch (Exception e) {
            throw new ServletException("Unable to sign in. Check the database connection.", e);
        }

        request.setAttribute("error", "Username or password is incorrect.");
        request.getRequestDispatcher("/login.jsp").forward(request, response);
    }

    private String hash(String value) throws Exception {
        // Demo project password hashing. For production, use a salted password-hashing algorithm.
        MessageDigest digest = MessageDigest.getInstance("SHA-256");
        byte[] bytes = digest.digest(value.getBytes(java.nio.charset.StandardCharsets.UTF_8));
        StringBuilder result = new StringBuilder();
        for (byte b : bytes) {
            result.append(String.format("%02x", b));
        }
        return result.toString();
    }
}
