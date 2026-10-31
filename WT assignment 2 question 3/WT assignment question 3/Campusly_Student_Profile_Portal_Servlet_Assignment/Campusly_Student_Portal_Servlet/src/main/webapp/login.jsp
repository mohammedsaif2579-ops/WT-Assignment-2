<%@ page contentType="text/html;charset=UTF-8" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | Campusly</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <h1>Campusly</h1>
        <p class="muted">Student Profile Portal</p>
        <% if (request.getAttribute("error") != null) { %>
            <p class="error"><%= request.getAttribute("error") %></p>
        <% } %>
        <form action="login" method="post">
            <label for="username">Username</label>
            <input id="username" name="username" required>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            <button type="submit">Sign in</button>
        </form>
        <p class="hint">Demo login: admin / admin123</p>
    </main>
</body>
</html>
