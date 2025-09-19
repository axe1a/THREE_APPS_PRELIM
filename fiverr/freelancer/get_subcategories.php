<?php
require_once 'classloader.php';

header('Content-Type: application/json');

if (isset($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];
    $subcategories = $subcategoryObj->getSubcategoriesByCategory($category_id);
    echo json_encode($subcategories);
} else {
    echo json_encode([]);
}
?>
