<?php

function getImagePath($relativePath) {
    if (empty($relativePath)) {
        return '';
    }

    // External full URLs are returned as-is
    if (preg_match('/^https?:\/\//i', $relativePath)) {
        return $relativePath;
    }

    // Always return absolute path from project root so it works in admin/writer/root
    // Adjust \\newspeper\\ below if your folder name changes
    return '/newspeper/' . ltrim($relativePath, '/');
}

// Helper function to display images safely
function displayImage($image_url) {
    if (!empty($image_url)) {
        $clean_url = htmlspecialchars(getImagePath($image_url));
        echo '<img src="' . $clean_url . '" 
                alt="Article Image" 
                class="img-fluid mb-3" 
                style="max-height:250px; object-fit:cover; width:100%;">';
    }
}

?>