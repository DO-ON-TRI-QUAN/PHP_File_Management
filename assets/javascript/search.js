// ============================================================
// Filters the file table in real time as the user types or
// selects a filter. No page reload needed.
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    const search_input  = document.getElementById('search_input');
    const filter_select = document.getElementById('filter_select');
    const table_rows    = document.querySelectorAll('#file_table tbody tr');

    // Reads current search term and filter selection, then
    // shows or hides each row accordingly.
    function apply_filters() {
        const search_term   = search_input.value.toLowerCase().trim();
        const filter_value  = filter_select.value;

        table_rows.forEach(function(row) {
            const name_cell = row.querySelector('.file_name');
            const type_cell = row.querySelector('.file_type');

            if (!name_cell || !type_cell) return;

            const file_name = name_cell.textContent.toLowerCase();
            const file_type = type_cell.textContent.trim();

            // Check search term match
            const matches_search = file_name.includes(search_term);

            // Check filter match
            const matches_filter = filter_value === 'all' || file_type === filter_value;

            // Show row only if both conditions are met
            row.style.display = (matches_search && matches_filter) ? '' : 'none';
        });
    }

    // Trigger filter on search input
    search_input.addEventListener('input', apply_filters);

    // Trigger filter on dropdown change
    filter_select.addEventListener('change', apply_filters);
});