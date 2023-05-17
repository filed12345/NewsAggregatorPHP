<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="/NewsWebSitePhp/public/css/register.css">
</head>
<body>
<div class="page-container">
    <div class="login-container">
            <h2>Register</h2>
            <form class="login-form" action="/NewsWebSitePhp/public/register" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                    <?php if (isset($username_error)) echo "<p style='color:red;font-weight: bold;'>$username_error</p>"; ?>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                    <?php if (isset($email_error)) echo "<p style='color:red;font-weight: bold;'>$email_error</p>"; ?>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button class="submit-btn" type="submit">Register</button>
                </div>
            </form>
            <div class="register-link">
                <p>Already registered? <a href="http://localhost/NewsWebSitePhp/public/login">Login</a></p>
            </div>
    </div>
</div>
<?php require '../app/views/footer.php'; ?>
</body>
</html>

