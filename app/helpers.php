<?php
/**
 * Global Helper Functions
 */

/**
 * The main view rendering function.
 * It intelligently loads the correct layout file and injects the page content.
 *
 * @param string $page The view file for the page content (e.g., 'pages.home').
 * @param array $data Data to be extracted for both layout and page.
 * @param string $layout The layout file to use (e.g., 'layouts.app').
 */
if (!function_exists('view')) {
    function view(string $page, array $data = [], string $layout = 'layouts.app'): void
    {
        // Add the page path to the data array so the layout knows what content to include.
        $data['page'] = $page; 

        extract($data);

        // Require the specified layout file.
        $layoutPath = VIEWS_PATH . '/' . str_replace('.', '/', $layout) . '.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
             die("Layout file not found: {$layoutPath}");
        }
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path)
    {
        header("Location: {$path}");
        exit();
    }
}