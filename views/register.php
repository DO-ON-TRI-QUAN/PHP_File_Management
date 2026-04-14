<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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

<h2>Register</h2>
<form method="POST" action="../controllers/AuthController.php?action=register">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="index.php?page=login">Login</a></p>
</body>
</html>