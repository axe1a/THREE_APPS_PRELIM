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

if (isset($_POST['insertArticleBtn'])) {
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
	echo $articleObj->deleteArticle($article_id);
}

if (isset($_POST['requestEdit'])) {
    $article_id = (int)$_POST['article_id'];
    $requester_id = $_SESSION['user_id'];
    $article = $articleObj->getArticles($article_id);
    if ($article) {
        $ok = $articleObj->createEditRequest($article_id, $requester_id);
        if ($ok) {
            $message = 'New edit request from ' . $_SESSION['username'] . ' for your article: ' . $article['title'];
            $articleObj->addNotification($article['author_id'], $message);
            header('Location: ../index.php');
            exit;
        }
    }
    echo 'Failed to request edit';
    exit;
}

