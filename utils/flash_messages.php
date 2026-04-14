<?php

// ============================================================
// Temporary session messages that display once after a redirect
// and are immediately cleared from the session.
// ============================================================


// Stores a flash message in the session.
// Type is either 'success' or 'error'.
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}


// Retrieves and clears the flash message from the session.
// Returns null if no message is set.
function get_flash() {
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']); // Clear after reading (show only once)
    return $flash;
}