<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/NewsWebSitePhp/public/css/loginpage.css">
</head>
<body>
<div class="page-container">
    <div class="login-container">
        <h2>Login</h2>
        <form class="login-form" action="http://localhost/NewsWebSitePhp/public/login" method="POST">
            <?php if (isset($error)) echo "<p style='color:red;font-weight: bold;'>$error</p>"; ?>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button class="submit-btn" type="submit">Login</button>
            </div>
        </form>
        <p class="register-link">Not registered yet? <a href="/NewsWebSitePhp/public/register">Register</a></p>
    </div>
</div>
<?php require '../app/views/footer.php'; ?>
</body>
</html>

