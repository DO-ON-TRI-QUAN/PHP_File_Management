<!DOCTYPE html>
<html>
<head>
    <title>File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-6xl mx-auto p-6">

<?php
$base_url   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$share_base = $base_url . '/public/index.php?page=share&token=';
?>

<?php 
require_once '../utils/flash_messages.php';
$flash = get_flash(); 
?>
<?php if ($flash): ?>
    <div class="mb-4 px-4 py-2 rounded border 
        <?php echo $flash['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">File Manager</h2>
    <a href="?page=logout" class="text-sm text-red-500 hover:underline">Logout</a>
</div>

<!-- Upload -->
<div class="bg-white p-4 rounded shadow-sm mb-6">
    <h3 class="font-medium mb-2">Upload File</h3>
    <p class="text-sm text-gray-500 mb-3">JPEG, PNG, PDF — max 5MB</p>

    <form action="../controllers/FileController.php?action=upload" method="POST" enctype="multipart/form-data" class="flex gap-2">
        <input type="file" name="file" required class="border p-2 rounded w-full">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
    </form>
</div>

<!-- Search -->
<div class="flex gap-2 mb-4">
    <input id="search_input" type="text" placeholder="Search files..."
        class="border p-2 rounded w-full">

    <select id="filter_select" class="border p-2 rounded">
        <option value="all">All</option>
        <option value="[IMG]">Images</option>
        <option value="[PDF]">PDF</option>
        <option value="[DOC]">Documents</option>
        <option value="[XLS]">Spreadsheets</option>
        <option value="[TXT]">Text</option>
        <option value="[ZIP]">Archives</option>
        <option value="[FILE]">Other</option>
    </select>
</div>

<!-- File Table -->
<div class="bg-white rounded shadow-sm overflow-hidden">
<?php if (!empty($files)): ?>
<table class="w-full text-sm" id="file_table">
    <thead class="bg-gray-50 text-gray-600">
        <tr>
            <th class="p-3 text-left">Type</th>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-center">Size</th>
            <th class="p-3 text-center">Uploaded</th>
            <th class="p-3 text-center">Visibility</th>
            <th class="p-3 text-center">Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($files as $file): ?>
        <tr class="border-t hover:bg-gray-50">
            <td class="p-3 file_type"><?php echo get_file_icon($file['original_name']); ?></td>

            <td class="p-3 file_name font-medium">
                <?php echo htmlspecialchars($file['original_name']); ?>
            </td>

            <td class="p-3 text-center"><?php echo format_file_size($file['file_size']); ?></td>
            <td class="p-3 text-center"><?php echo format_date($file['uploaded_at']); ?></td>

            <td class="p-3 text-center">
                <span class="<?php echo $file['visibility'] === 'public' ? 'text-green-600' : 'text-gray-500'; ?>">
                    <?php echo ucfirst($file['visibility']); ?>
                </span>
            </td>

            <td class="p-3 text-center space-x-2">

                <a class="text-blue-600 hover:underline"
                    href="../controllers/FileController.php?action=download&id=<?php echo $file['id']; ?>">
                    Download
                </a>

                <button class="text-yellow-600"
                    onclick="openModal('rename', <?php echo $file['id']; ?>, '<?php echo htmlspecialchars($file['original_name']); ?>')">
                    Rename
                </button>

                <button class="text-purple-600"
                    onclick="openModal('toggle', <?php echo $file['id']; ?>)">
                    Toggle
                </button>

                <button class="text-red-600"
                    onclick="openModal('delete', <?php echo $file['id']; ?>)">
                    Delete
                </button>

                <?php if ($file['visibility'] === 'public' && $file['share_token']): ?>
                    <button class="text-green-600"
                        onclick="openModal('share', null, '<?php echo $share_base . $file['share_token']; ?>')">
                        Share
                    </button>
                <?php endif; ?>

            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="p-4 text-gray-500">No files uploaded.</div>
<?php endif; ?>
</div>

</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-30 items-center justify-center" style="display:none;">
    <div class="bg-white rounded shadow p-6 w-80">

        <h3 id="modal_title" class="text-lg font-medium mb-4"></h3>

        <form id="modal_form" method="POST">
            <input type="hidden" name="id" id="modal_id">

            <div id="modal_body"></div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal()" class="px-3 py-1 border rounded">
                    Cancel
                </button>
                <button id="modal_confirm" type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">
                    Confirm
                </button>
            </div>
        </form>

    </div>
</div>

<script src="../assets/javascript/modal.js"></script>
<script src="../assets/javascript/search.js"></script>

</body>
</html>