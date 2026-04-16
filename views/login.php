<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-6 rounded shadow w-80">

<?php
require_once '../utils/flash_messages.php';
$flash = get_flash();
?>
<?php if ($flash): ?>
    <div class="mb-4 text-sm <?php echo $flash['type'] === 'success' ? 'text-green-600' : 'text-red-600'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<h2 class="text-xl font-semibold mb-4 text-center">Login</h2>

<form method="POST" action="../controllers/AuthController.php?action=login" class="space-y-3">
    <input type="email" name="email" placeholder="Email" required class="w-full border p-2 rounded">
    <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded">

    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
        Login
    </button>
</form>

<p class="text-sm text-center mt-4">
    <a href="index.php?page=register" class="text-blue-600 hover:underline">Create account</a>
</p>

</div>
</body>
</html>