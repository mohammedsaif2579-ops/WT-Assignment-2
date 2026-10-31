package com.campusly;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DBConnection {
    private static final String URL =
            "jdbc:mysql://localhost:3306/campusly_portal?useSSL=false&serverTimezone=UTC";
    private static final String USER = "root";
    private static final String PASSWORD = "";

    public static Connection getConnection() throws SQLException {
        // Open a fresh database connection for the current request.
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }
}
