/**
 * VillaStudio Main Application JavaScript
 *
 * Handles global interactivity: sticky header, mobile navigation, and search modal.
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('VillaStudio Initialized: DOM Ready.');

    // --- Element Selections ---
    const header = document.querySelector('.main-header');
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mainNav = document.querySelector('.main-nav');
    const searchOpenBtn = document.querySelector('.search-btn');
    const searchModal = document.querySelector('.search-modal');
    const searchModalClose = document.querySelector('.search-modal-close');
    const searchInput = document.getElementById('searchInput');

    // --- 1. Sticky Header ---
    const handleStickyHeader = () => {
        if (header && window.scrollY > 50) {
            header.classList.add('is-sticky');
        } else if (header) {
            header.classList.remove('is-sticky');
        }
    };
    window.addEventListener('scroll', handleStickyHeader);
    handleStickyHeader(); // Run on page load

    // --- 2. Mobile Navigation Toggle ---
    if (mobileNavToggle && mainNav) {
        mobileNavToggle.addEventListener('click', () => {
            mainNav.classList.toggle('is-open');
            mobileNavToggle.classList.toggle('is-active');
            document.body.classList.toggle('no-scroll'); // Prevent scrolling when menu is open
        });
    }

    // --- 3. Search Modal Functionality (THIS IS THE FIX) ---
    const openSearchModal = () => {
        if (searchModal && searchInput) {
            searchModal.classList.add('is-active');
            document.body.classList.add('no-scroll');
            // Use a slight delay to ensure the element is visible before focusing
            setTimeout(() => searchInput.focus(), 300);
        }
    };

    const closeSearchModal = () => {
        if (searchModal) {
            searchModal.classList.remove('is-active');
            document.body.classList.remove('no-scroll');
        }
    };

    if (searchOpenBtn && searchModal && searchModalClose && searchInput) {
        // Event listener to open the modal
        searchOpenBtn.addEventListener('click', openSearchModal);

        // Event listener to close the modal with the 'X' button
        searchModalClose.addEventListener('click', closeSearchModal);

        // Event listener to close the modal if the user clicks on the dark overlay
        searchModal.addEventListener('click', (e) => {
            if (e.target === searchModal) {
                closeSearchModal();
            }
        });

        // Event listener to close the modal with the 'Escape' key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchModal.classList.contains('is-active')) {
                closeSearchModal();
            }
        });
    }
});