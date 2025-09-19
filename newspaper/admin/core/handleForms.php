<?php  
require_once '../classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password)) {
					header("Location: ../login.php");
				}

				else {
					$_SESSION['message'] = "An error occured with the query!";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
				}
			}

			else {
				$_SESSION['message'] = $username . " as username is already taken";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
			}
		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	}
	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	if (!empty($email) && !empty($password)) {

		if ($userObj->loginUser($email, $password)) {
			header("Location: ../index.php");
		}
		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}

if (isset($_POST['insertAdminArticleBtn'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $author_id = $_SESSION['user_id'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $image_url = null;

    // Public uploads folder (relative to index.php)
    $uploads_dir = __DIR__ . '/../../img/';

    if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] == UPLOAD_ERR_OK) {
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $tmp_name = $_FILES['article_image']['tmp_name'];
        $basename = basename($_FILES['article_image']['name']);
        $new_filename = uniqid() . '_' . $basename;
        $target_file = $uploads_dir . $new_filename;

        if (move_uploaded_file($tmp_name, $target_file)) {
            // Save clean relative path in DB
            $image_url = 'img/' . $new_filename;
        }
    }

    if ($articleObj->createArticle($title, $description, $author_id, $category_id, $image_url)) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Failed to create article.";
        exit;
    }
}

if (isset($_POST['editArticleBtn'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$article_id = $_POST['article_id'];
	$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
	if ($articleObj->updateArticle($article_id, $title, $description, $category_id)) {
		header("Location: ../articles_submitted.php");
	}
}

if (isset($_POST['deleteArticleBtn'])) {
	$article_id = $_POST['article_id'];
	// Fetch article to notify author after deletion
	$article = $articleObj->getArticles($article_id);
	$author_id = $article ? $article['author_id'] : null;
	$result = $articleObj->deleteArticle($article_id);
	echo $result;
	if ($result && $author_id) {
		$message = 'Your article "' . $article['title'] . '" was deleted by an admin.';
		$articleObj->addNotification($author_id, $message);
	}
}

if (isset($_POST['updateArticleVisibility'])) {
	$article_id = $_POST['article_id'];
	$status = $_POST['status'];
	echo $articleObj->updateArticleVisibility($article_id,$status);
}

// Admin responds to edit requests
if (isset($_POST['respondEditRequest'])) {
    $article_id = (int)$_POST['article_id'];
    $requester_id = (int)$_POST['requester_id'];
    $decision = $_POST['decision']; // 'accepted' or 'rejected'
    if (in_array($decision, ['accepted','rejected'])) {
        $ok = $articleObj->updateEditRequestStatus($article_id, $requester_id, $decision);
        if ($ok) {
            $msg = $decision === 'accepted' ? 'Your edit request was accepted.' : 'Your edit request was rejected.';
            $article = $articleObj->getArticles($article_id);
            if ($article) {
                $msg = $msg . ' Article: ' . $article['title'];
            }
            $articleObj->addNotification($requester_id, $msg);
            echo 1; exit;
        }
    }
    echo 0; exit;
}

// Category management
if (isset($_POST['addCategoryBtn'])) {
    $name = trim($_POST['category_name']);
    $description = trim($_POST['category_description']);
    
    if (!empty($name)) {
        if (!$categoryObj->categoryNameExists($name)) {
            if ($categoryObj->createCategory($name, $description)) {
                $_SESSION['message'] = "Category added successfully!";
                $_SESSION['status'] = '200';
            } else {
                $_SESSION['message'] = "Failed to add category.";
                $_SESSION['status'] = '400';
            }
        } else {
            $_SESSION['message'] = "Category name already exists.";
            $_SESSION['status'] = '400';
        }
    } else {
        $_SESSION['message'] = "Category name is required.";
        $_SESSION['status'] = '400';
    }
    header("Location: ../manage_categories.php");
    exit;
}

if (isset($_POST['updateCategoryBtn'])) {
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['category_name']);
    $description = trim($_POST['category_description']);
    
    if (!empty($name) && $category_id > 0) {
        if (!$categoryObj->categoryNameExists($name, $category_id)) {
            if ($categoryObj->updateCategory($category_id, $name, $description)) {
                $_SESSION['message'] = "Category updated successfully!";
                $_SESSION['status'] = '200';
            } else {
                $_SESSION['message'] = "Failed to update category.";
                $_SESSION['status'] = '400';
            }
        } else {
            $_SESSION['message'] = "Category name already exists.";
            $_SESSION['status'] = '400';
        }
    } else {
        $_SESSION['message'] = "Category name is required.";
        $_SESSION['status'] = '400';
    }
    header("Location: ../manage_categories.php");
    exit;
}

if (isset($_POST['deleteCategoryBtn'])) {
    $category_id = (int)$_POST['category_id'];
    
    if ($category_id > 0) {
        if ($categoryObj->deleteCategory($category_id)) {
            echo 1;
        } else {
            echo 0;
        }
    } else {
        echo 0;
    }
    exit;
}