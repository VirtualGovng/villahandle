document.addEventListener('DOMContentLoaded', () => {
    // Mobile sidebar toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }

    // Delete confirmation prompt
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (event) => {
            const confirmation = confirm('Are you sure you want to delete this item? This action cannot be undone.');
            if (!confirmation) {
                event.preventDefault();
            }
        });
    });
});