<?php

// ============================================================
// Utility functions 
// ============================================================


// Converts a raw byte count into a human readable string.
function format_file_size($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB'; // 1048576 B -> 1.00 MB
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB'; // 1024 B -> 1.00 KB
    } else {
        return $bytes . ' B';
    }
}

// Returns a simple text label representing the file type
// based on the file's original name extension.
function get_file_icon($original_name) {
    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'jpg':
        case 'jpeg':
        case 'png':
            return '[IMG]';
        case 'pdf':
            return '[PDF]';
        default:
            return '[FILE]';
    }
}

// Formats a MySQL timestamp into a readable date string.
// Example: "1970-01-01 12:30:00" → "Jan 1, 1970 12:30"
function format_date($timestamp) {
    return date('M d, Y H:i', strtotime($timestamp));
}