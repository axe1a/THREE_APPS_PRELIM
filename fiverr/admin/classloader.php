<?php  
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Category.php';
require_once 'classes/Subcategory.php';

$databaseObj = new Database();
$userObj = new User();
$categoryObj = new Category();
$subcategoryObj = new Subcategory();

$userObj->startSession();
?>
