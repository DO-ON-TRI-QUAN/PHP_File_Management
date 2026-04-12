<!DOCTYPE html>
<html>
<head>
    <title>File Manager</title>
</head>
<body>

<h2>File Manager</h2>

<!-- Upload form -->
<h3>Upload File</h3>
<p>Allowed types: JPEG, PNG, PDF — Maximum size: 5MB</p>
<form action="../controllers/FileController.php?action=upload" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

<hr>

<!-- File list -->
<h3>Your Files</h3>
<?php if (count($files) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Size</th>
                <th>Uploaded</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?php echo get_file_icon($file['original_name']); ?></td>
                    <td><?php echo htmlspecialchars($file['original_name']); ?></td>
                    <td><?php echo format_file_size($file['file_size']); ?></td>
                    <td><?php echo format_date($file['uploaded_at']); ?></td>
                    <td>
                        <!-- Download -->
                        <a href="../controllers/FileController.php?action=download&id=<?php echo $file['id']; ?>">Download</a>

                        <!-- Rename -->
                        <form action="../controllers/FileController.php?action=rename" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $file['id']; ?>">
                            <input type="text" name="new_name" placeholder="New name (without extension)" required>
                            <button type="submit">Rename</button>
                        </form>

                        <!-- Delete -->
                        <a href="../controllers/FileController.php?action=delete&id=<?php echo $file['id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this file?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No files uploaded yet.</p>
<?php endif; ?>

<hr>

<a href="?page=logout">Logout</a>

</body>
</html>