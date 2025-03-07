<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration and Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>User Registration and Login</h1>

    <h2>Register</h2>
    <form action="api/user.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="number" name="permissions" placeholder="Permissions (0-3)" required>
        <button type="submit">Register</button>
    </form>

    <h2>Login</h2>
    <form action="api/user.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <h2>Upload File</h2>
    <form action="api/user.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".zip,.clip" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
