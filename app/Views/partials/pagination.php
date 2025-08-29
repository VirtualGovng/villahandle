<?php if (isset($totalPages) && $totalPages > 1): ?>
<nav class="pagination-container" aria-label="Page navigation">
    <ul class="pagination">
        <?php
        // Determine the base URL (either /movies or /search)
        $baseUrl = (strpos($_SERVER['REQUEST_URI'], '/search') === 0) ? '/search' : '/movies';
        
        // Start with existing query params (like 'q' or 'genre')
        $queryParams = $_GET;

        // Previous Button
        if ($currentPage > 1) {
            $queryParams['page'] = $currentPage - 1;
            echo '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">&laquo; Prev</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>';
        }

        // Page Number Links
        for ($i = 1; $i <= $totalPages; $i++) {
            $queryParams['page'] = $i;
            if ($i == $currentPage) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
            }
        }

        // Next Button
        if ($currentPage < $totalPages) {
            $queryParams['page'] = $currentPage + 1;
            echo '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">Next &raquo;</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
        }
        ?>
    </ul>
</nav>
<?php endif; ?>