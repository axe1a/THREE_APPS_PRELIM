<?php

function getImagePath($relativePath) {
    // Get the current directory structure
    $currentDir = dirname($_SERVER['PHP_SELF']);
    $baseDir = basename($currentDir);
    
    // If we're in admin or writer folder, go up one level
    if ($baseDir === 'writer' || $baseDir === 'admin') {
        return '../' . $relativePath;
    }
    return $relativePath;
}

// Helper function to display images safely
function displayImage($image_url) {
    if (!empty($image_url)) {
        $clean_url = htmlspecialchars($image_url);
        echo '<img src="' . getImagePath($clean_url) . '" 
                alt="Article Image" 
                class="img-fluid mb-3" 
                style="max-height:250px; object-fit:cover; width:100%;">';
    }
}

?>