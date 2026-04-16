// ============================================================
// Handles all modal interactions.
// Dynamically updates modal content and form actions without reloading the page.
// ============================================================

function openModal(type, id = null, extra = null) {
    const modal = document.getElementById('modal');
    const title = document.getElementById('modal_title');
    const body = document.getElementById('modal_body');
    const form = document.getElementById('modal_form');
    const inputId = document.getElementById('modal_id');
    const confirmBtn = document.getElementById('modal_confirm');

    modal.style.display = 'flex';
    inputId.value = id || '';
    confirmBtn.style.display = 'inline-block';

    if (type === 'rename') {
        title.innerText = "Rename File";
        form.action = "../controllers/FileController.php?action=rename";
        let name = extra.replace(/\.[^/.]+$/, "");
        body.innerHTML = `
            <input type="text" name="new_name" value="${name}"
            class="w-full border p-2 rounded" required>
        `;
    }
    if (type === 'delete') {
        title.innerText = "Delete File";
        form.action = "../controllers/FileController.php?action=delete";
        body.innerHTML = `
            <p class="text-sm text-gray-600">
                Are you sure you want to delete this file?
            </p>
        `;
    }
    if (type === 'toggle') {
        title.innerText = "Toggle Visibility";
        form.action = "../controllers/FileController.php?action=toggle_visibility";
        body.innerHTML = `
            <p class="text-sm text-gray-600">
                Change file visibility?
            </p>
        `;
    }
    if (type === 'share') {
        title.innerText = "Share Link";
        body.innerHTML = `
            <input type="text" value="${extra}" class="w-full border p-2 rounded mb-2" readonly>
            <button type="button" class="text-blue-600" onclick="navigator.clipboard.writeText('${extra}')">
                Copy Link
            </button>
        `;
        form.action = "#";
        confirmBtn.style.display = 'none';
    }
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
}

// Click outside window will close modal
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal');
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});