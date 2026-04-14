<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<!-- Flash message -->
<?php
require_once '../utils/flash_messages.php';
$flash = get_flash();
?>
<?php if ($flash): ?>
    <p style="color: <?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>;">
        <?php echo htmlspecialchars($flash['message']); ?>
    </p>
<?php endif; ?>

<h2>Login</h2>
<form method="POST" action="../controllers/AuthController.php?action=login">
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="index.php?page=register">Register</a></p>
</body>
</html>