<!DOCTYPE html>
<html>
<head>
    <title>File Manager</title>
</head>
<body>

<!-- ============================================================
     HOME PAGE
     Displays the upload form and the current user's file list.
     $files is passed in from index.php
     ============================================================ -->

<h2>File Manager</h2>

<!-- Upload form -->
<h3>Upload File</h3>
<form action="../controllers/FileController.php?action=upload" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

<hr>

<!-- File list -->
<h3>Your Files</h3>
<?php if (count($files) > 0): ?>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <?php echo htmlspecialchars($file['original_name']); ?>
                <a href="../controllers/FileController.php?action=download&id=<?php echo $file['id']; ?>">Download</a>
                <a href="../controllers/FileController.php?action=delete&id=<?php echo $file['id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this file?')">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No files uploaded yet.</p>
<?php endif; ?>

<hr>

<a href="?page=logout">Logout</a>

</body>
</html>