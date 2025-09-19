<?php  
require_once 'classes/Database.php';
require_once 'classes/Offer.php';
require_once 'classes/Proposal.php';
require_once 'classes/User.php';
require_once 'classes/Category.php';
require_once 'classes/Subcategory.php';

$databaseObj= new Database();
$offerObj = new Offer();
$proposalObj = new Proposal();
$userObj = new User();
$categoryObj = new Category();
$subcategoryObj = new Subcategory();

$userObj->startSession();
?>