<?php  
require_once '../classloader.php';

// Admin login
if (isset($_POST['loginAdminBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);
	if (!empty($email) && !empty($password)) {
		if ($userObj->loginUser($email, $password) && $userObj->isFiverrAdministrator()) {
			header("Location: ../index.php");
			exit;
		}
		// If logged in but not admin, or login failed, force logout and show error
		$userObj->logout();
		$_SESSION['message'] = "Administrator credentials required.";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
		exit;
	} else {
		$_SESSION['message'] = "Please fill in both email and password.";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
		exit;
	}
}

// Admin registration
if (isset($_POST['insertNewAdminBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$contact_number = htmlspecialchars(trim($_POST['contact_number']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);
	$role_id = 3; // fiverr_administrator

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
		if ($password === $confirm_password) {
			if (!$userObj->usernameExists($username)) {
				if ($userObj->registerUser($username, $email, $password, $contact_number, $role_id)) {
					$_SESSION['message'] = "Administrator account created. You can now login.";
					$_SESSION['status'] = '200';
					header("Location: ../login.php");
					exit;
				} else {
					$_SESSION['message'] = "An error occurred while creating admin.";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
					exit;
				}
			} else {
				$_SESSION['message'] = $username . " is already taken.";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
				exit;
			}
		} else {
			$_SESSION['message'] = "Passwords do not match.";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
			exit;
		}
	} else {
		$_SESSION['message'] = "Please fill all fields.";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
		exit;
	}
}

// Admin Panel - Category Management
if (isset($_POST['addCategoryBtn'])) {
	$category_name = htmlspecialchars(trim($_POST['category_name']));
	$category_description = htmlspecialchars(trim($_POST['category_description']));
	
	if (!empty($category_name)) {
		if ($categoryObj->createCategory($category_name, $category_description)) {
			$_SESSION['message'] = "Category added successfully!";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Error adding category. Please try again.";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "Category name is required.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_POST['addSubcategoryBtn'])) {
	$category_id = $_POST['category_id'];
	$subcategory_name = htmlspecialchars(trim($_POST['subcategory_name']));
	$subcategory_description = htmlspecialchars(trim($_POST['subcategory_description']));
	
	if (!empty($category_id) && !empty($subcategory_name)) {
		if ($subcategoryObj->createSubcategory($category_id, $subcategory_name, $subcategory_description)) {
			$_SESSION['message'] = "Subcategory added successfully!";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Error adding subcategory. Please try again.";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "Category and subcategory name are required.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_GET['deactivateCategory'])) {
	$category_id = $_GET['deactivateCategory'];
	if ($categoryObj->deleteCategory($category_id)) {
		$_SESSION['message'] = "Category deactivated successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Error deactivating category.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_GET['activateCategory'])) {
	$category_id = $_GET['activateCategory'];
	if ($categoryObj->activateCategory($category_id)) {
		$_SESSION['message'] = "Category activated successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Error activating category.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_GET['deactivateSubcategory'])) {
	$subcategory_id = $_GET['deactivateSubcategory'];
	if ($subcategoryObj->deleteSubcategory($subcategory_id)) {
		$_SESSION['message'] = "Subcategory deactivated successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Error deactivating subcategory.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_GET['activateSubcategory'])) {
	$subcategory_id = $_GET['activateSubcategory'];
	if ($subcategoryObj->activateSubcategory($subcategory_id)) {
		$_SESSION['message'] = "Subcategory activated successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Error activating subcategory.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

// User Management
if (isset($_GET['deleteUser'])) {
	$user_id = $_GET['deleteUser'];
	if ($user_id != $_SESSION['user_id']) { // Prevent self-deletion
		if ($userObj->deleteUser($user_id)) {
			$_SESSION['message'] = "User deleted successfully!";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Error deleting user.";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "You cannot delete your own account.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../users.php");
}

if (isset($_POST['updateUserBtn'])) {
	$user_id = $_POST['user_id'];
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$contact_number = htmlspecialchars(trim($_POST['contact_number']));
	$bio_description = htmlspecialchars(trim($_POST['bio_description']));
	$role_id = (int)$_POST['role_id'];
	
	if ($userObj->updateUser($user_id, $username, $email, $contact_number, $bio_description, $role_id)) {
		$_SESSION['message'] = "User updated successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Error updating user.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../users.php");
}

// Logout
if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../login.php");
}
?>
