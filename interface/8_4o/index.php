<?php
//Modified for The Front Street Advertising Project
/**
 * Class OneFileLoginApplication
 *
 * An entire php application with user registration, login and logout in one file.
 * Uses very modern password hashing via the PHP 5.5 password hashing functions.
 * This project includes a compatibility file to make these functions available in PHP 5.3.7+ and PHP 5.4+.
 *
 * @author Panique
 * @link https://github.com/panique/php-login-one-file/
 * @license http://opensource.org/licenses/MIT MIT License
 */

class OneFileLoginApplication
{
    private $db_type = "sqlite";
    private $db_sqlite_path = "front_street_advertising_project.sqlite3";
    private $db_connection = null;
    private $user_is_logged_in = false;
    /**
	Default is "frontstreet"
	BCrypt Hash from https://bcrypt-generator.com/
    **/
    private $reg_key_hash = '$2y$12$1knrlq63p7kvFzVZch6oSe53cvumcI0Ue1oUrR0aciC2M7RlhuuR6';

    public $feedback = "";
    public function __construct()
    {
        if ($this->performMinimumRequirementsCheck()) {
            $this->runApplication();
        }
    }

    private function performMinimumRequirementsCheck()
    {
        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            echo "Sorry, Simple PHP Login does not run on a PHP version older than 5.3.7 !";
        } elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
            require_once("libraries/password_compatibility_library.php");
            return true;
        } elseif (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            return true;
        }
                return false;
    }

    public function runApplication()
    {
	require 'db_create.php';
        if (isset($_GET["action"]) && $_GET["action"] == "register") {
            $this->doRegistration();
            $this->showPageRegistration();
        } else if (isset($_GET["action"]) && $_GET["action"] == "createdb") {
	    createDB();
            $this->doRegistration();
            $this->showPageRegistration();
        } else if (isset($_GET["action"]) && $_GET["action"] == "submission") {
            $this->showPageSubmission();
        } else if (isset($_GET["action"]) && $_GET["action"] == "upload") {
            $this->fileUpload();
            $this->showPageSubmission();
        } else {
            $this->doStartSession();
            $this->performUserLoginAction();
            if ($this->getUserLoginStatus()) {
                $this->showPageLoggedIn();
            } else {
                $this->showPageLoginForm();
            }
        }
    }

    private function createDatabaseConnection()
    {
	try {
		$this->db_connection = new PDO($this->db_type . ':' . $this->db_sqlite_path);
		return true;
	} catch (PDOException $e) {
		$this->feedback = "PDO database connection problem: " . $e->getMessage();
	} catch (Exception $e) {
		$this->feedback = "General problem: " . $e->getMessage();
	}
        return false;
    }

    private function performUserLoginAction()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "logout") {
            $this->doLogout();
        } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
            $this->doLoginWithSessionData();
        } elseif (isset($_POST["login"])) {
            $this->doLoginWithPostData();
        }
    }

    private function doStartSession()
    {
        if(session_status() == PHP_SESSION_NONE) session_start();
    }

    private function doLoginWithSessionData()
    {
        $this->user_is_logged_in = true;     }

    private function doLoginWithPostData()
    {
        if ($this->checkLoginFormDataNotEmpty()) {
            if ($this->createDatabaseConnection()) {
                $this->checkPasswordCorrectnessAndLogin();
            }
        }
    }

    private function doLogout()
    {
	$_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
        $this->feedback = "You were just logged out.";
    }

    private function doRegistration()
    {
        if ($this->checkRegistrationData()) {
            if ($this->createDatabaseConnection()) {
                $this->createNewUser();
            }
        }
                return false;
    }

    private function checkLoginFormDataNotEmpty()
    {
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->feedback = "Password field was empty.";
        }
                return false;
    }

    private function checkPasswordCorrectnessAndLogin()
    {
        $sql = 'SELECT user_name, user_password_hash
                FROM users
                WHERE user_name = :user_name
                LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $_POST['user_name']);
        $query->execute();
        $result_row = $query->fetchObject();
        if ($result_row) {
                        if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {
                                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_is_logged_in'] = true;
                $this->user_is_logged_in = true;
                return true;
            } else {
                $this->feedback = "Wrong password.";
            }
        } else {
            $this->feedback = "This user does not exist.";
        }
                return false;
    }

    private function checkRegistrationData()
    {
                if (!isset($_POST["register"])) {
            return false;
        }

                if (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['reg_key'])
            && strlen($_POST['reg_key']) <= 64
            && !empty($_POST['user_password_new'])
            && strlen($_POST['user_password_new']) >= 6
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
                        return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Empty Username";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->feedback = "Empty Password";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->feedback = "Password and password repeat are not the same";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->feedback = "Password has a minimum length of 6 characters";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->feedback = "Username cannot be shorter than 2 or longer than 64 characters";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->feedback = "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
        } elseif (empty($_POST['reg_key'])) {
            $this->feedback = "Registration Key cannot be empty";
        } elseif (strlen($_POST['reg_key']) > 64) {
            $this->feedback = "Registration Key cannot be longer than 64 characters";
        } else {
            $this->feedback = "An unknown error occurred.";
        }

                return false;
    }

    private function createNewUser()
    {
        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES);
        $reg_key = htmlentities($_POST['reg_key'], ENT_QUOTES);
        $user_password = $_POST['user_password_new'];
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
        $sql = 'SELECT * FROM users WHERE user_name = :user_name';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute();
        $result_row = $query->fetchObject();

	if ($result_row) {
            $this->feedback = "Sorry, that username is already taken. Please choose another one.";
        } else {
 	    if (password_verify($_POST['reg_key'], $this->reg_key_hash)) {
	            $sql = 'INSERT INTO users (user_name, user_password_hash)
	                VALUES(:user_name, :user_password_hash)';
        	    $query = $this->db_connection->prepare($sql);
		    $query->bindValue(':user_name', $user_name);
	            $query->bindValue(':user_password_hash', $user_password_hash);
                                            $registration_success_state = $query->execute();

            if ($registration_success_state) {
                $this->feedback = "Your account has been created successfully. You can now log in.";
                return true;
		}
            } else {
                $this->feedback = "Sorry, your registration failed. Please try again.";
            }
        }
                return false;
    }

    public function getUserLoginStatus()
    {
        return $this->user_is_logged_in;
    }

    //https://www.w3schools.com/php/php_file_upload.asp
    private function fileUpload()
    {
	$target_dir = "../content/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image
//	if(isset($_POST["submit"])) {
	    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    if($check !== false) {
	        $this->feedback = "File is valid - " . $check["mime"] . ".";
	        $uploadOk = 1;
	    } else {
	        $this->feedback = "File is not valid.";
	        $uploadOk = 0;
	    }
//	}
	// Check if file already exists
	if (file_exists($target_file)) {
	    $this->feedback = "Sorry, file already exists.";
	    $uploadOk = 0;
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 500000) {
	    $this->feedback = "Sorry, your file is too large.";
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
	&& $fileType != "gif" && $fileType != "webm" && $fileType != "avi" 
	&& $fileType != "flv" && $fileType != "wmv" && $fileType != "mp4" 
	&& $fileType != "ogv" && $fileType != "ogg" && $fileType != "mpg" 
	&& $fileType != "mpeg" ) {
	    $this->feedback = "Only JPG, JPEG, PNG, GIF, WEBM, AVI, FLV, WMV, MP4, MPG, MPEG, OGV, & OGG files are allowed.";
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    $this->feedback = "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        $this->feedback = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	    } else {
	        $this->feedback = "Sorry, there was an error uploading your file.";
	    }
	}
    }

    private function showPageLoggedIn()
    {
		header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?action=submission');
    }

    private function showPageLoginForm()
    {
	echo "<html>";
	echo "<head>";
	echo "<title>Front Street Login</title>";
	echo "<meta charset='utf-8'>";
	echo "<link rel='icon' href='../images/favicon.ico' type='image/x-icon'>";
	echo "<link href='../css/login.css' rel='stylesheet' type='text/css'>";
	echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
	echo "<script type='application/x-javascript'> addEventListener('load', function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>";
	echo "<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lobster:200,300,400,600,700&amp;lang=en'>";
	echo "<link rel='icon' href='../images/favicon.ico' type='image/x-icon'>";
	echo "</head>";
	echo "<body>";
	echo "<br class='hidden_br'>";
	echo "<br class='hidden_br'>";
	echo "<div class='main'>";
	echo "<div class='login-form'>";
	echo "<div align='center'><img src='../images/logo.png' alt='Front Street' style='width:128px;height:auto;padding:10px;'></div>";
	if (!file_exists($this->db_sqlite_path)) {
		echo "<h1>Front Street<br>Login</h1>";
		echo "<br>";
		echo "<div style='width: 70%;margin: 0 auto;padding: 6% 0 9% 0;'>";
	        echo "<form method='post' action='" . $_SERVER['SCRIPT_NAME'] . "?action=createdb' name='createdb'>";
		echo "<input type='submit' value='Create User DB' name='Create User DB' />";
		echo "<div align='center' class='copy-right'>";
		echo "<p><a href='http://www.mjns.it' target='_blank'>MJ Network Solutions</a></p>";
		echo "</form>";
		echo "</div>";
	} else {
		echo "<h1>Front Street<br>Login</h1>";
		echo "<form method='post' action='" . $_SERVER['SCRIPT_NAME'] . "' name='loginform'>";
		echo "<div align='center' style='width: 80%; margin: 0 auto; padding: 6% 0 9% 0;'>";
		echo "<input id='login_input_username' name='user_name' type='text' class='text' placeholder='Username' autocapitalize='off' autocorrect='off' required />";
		echo "<input id='login_input_password' name='user_password' type='password' class='text' placeholder='Password' required />";
		echo "<div class='submit'>";
		echo "<input type='submit' value='LOGIN' name='login' />";
		if ($this->feedback) {
			echo "<h1 class='feedback'>" . $this->feedback . "</h1><br>";
		}
		echo "<div align='center' class='copy-right'>";
		echo "<p><a href='" . $_SERVER['SCRIPT_NAME'] . "?action=register'>Create Account</a></p><br>";
		echo "<p><a href='http://www.mjns.it' target='_blank'>MJ Network Solutions</a></p>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</form>";
	}
	echo "</div>";
	echo "</div>";
	echo "<script src='../js/core.min.js'></script>";
	echo "<script src='../js/fsap-login.js'></script>";
	echo "</body>";
	echo "</html>";
    }

    private function showPageRegistration()
    {
        echo "<html>";
        echo "<head>";
        echo "<title>Front Street Login</title>";
        echo "<meta charset='utf-8'>";
	echo "<link rel='icon' href='../images/favicon.ico' type='image/x-icon'>";
        echo "<link href='../css/login.css' rel='stylesheet' type='text/css' />";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
        echo "<script type='application/x-javascript'> addEventListener('load', function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>";
	echo "<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lobster:200,300,400,600,700&amp;lang=en'>";
        echo "</head>";
        echo "<body>";
        echo "<div class='main'>";
        echo "<div class='login-form'>";
	echo "<div align='center'><img src='../images/logo.png' alt='Front Street' style='width:128px;height:auto;padding:10px;'></div>";
        echo "<form method='post' action='" . $_SERVER['SCRIPT_NAME'] . "?action=register' name='registerform'>";
        echo "<h1>Front Street Registration</h1>";
        echo "<div align='center' style='width: 80%; margin: 0 auto; padding: 6% 0 9% 0;'>";
        echo "<input type='button' name='autogen' value='GENERATE' class='login-button' onclick='auto_generate_input(0);'/>";
        echo "<input id='login_input_username' type='text' pattern='[a-zA-Z0-9]{2,64}' name='user_name' placeholder='Username' required />";
        echo "<input id='login_input_reg_key' type='password' name='reg_key' placeholder='Registration Key' required autocomplete='off' />";
        echo "<input id='login_input_password_new' class='login_input' type='password' name='user_password_new' pattern='.{4,}' placeholder='Password (6 Characters Min)' required autocomplete='off' />";
        echo "<input id='login_input_password_repeat' class='login_input' type='password' name='user_password_repeat' pattern='.{4,}' placeholder='Repeat Password' required autocomplete='off' />";
        echo "<div class='submit'>";
        echo "<input type='submit' name='register' value='REGISTER' />";
        if ($this->feedback) {
		echo "<h1 class='feedback'>" . $this->feedback . "</h1><br>";
        }
        echo "<div align='center' class='copy-right'>";
        echo "<p><a href='" . $_SERVER['SCRIPT_NAME'] . "'>Back To Login</a></p><br>";
	echo "<p><a href='http://www.mjns.it' target='_blank'>MJ Network Solutions</a></p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
	echo "<script src='../js/core.min.js'></script>";
	echo "<script src='../js/fsap-login.js'></script>";
        echo "</body>";
        echo "</html>";
    }

    private function showPageSubmission()
    {
        echo "<html>";
        echo "<head>";
        echo "<title>Front Street Submission</title>";
        echo "<meta charset='utf-8'>";
	echo "<link rel='icon' href='../images/favicon.ico' type='image/x-icon'>";
        echo "<link href='../css/login.css' rel='stylesheet' type='text/css' />";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
        echo "<script type='application/x-javascript'> addEventListener('load', function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>";
	echo "<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lobster:200,300,400,600,700&amp;lang=en'>";
        echo "</head>";
        echo "<body>";
        echo "<div class='main'>";
        echo "<div class='login-form'>";
	echo "<div align='center'><img src='../images/logo.png' alt='Front Street' style='width:128px;height:auto;padding:10px;'></div>";
        echo "<h1>Front Street</br>Submission</h1>";
        echo "<div align='center' style='width: 70%; margin: 0 auto; padding: 6% 0 9% 0;'>";
        echo "<select id='content_type'>";
	echo "<option value=''>Select Content Type</option>";
	echo "<option value='image'>Image</option>";
	echo "<option value='video'>Video</option>";
        echo "</select>";
	echo "</br>";
        echo "<select id='ad_type'>";
	echo "<option value=''>Select Ad Type</option>";
	echo "<option value='tenant'>Tenant</option>";
	echo "<option value='business'>Business</option>";
	echo "<option value='sale'>Sale</option>";
	echo "<option value='announcement'>Announcement</option>";
        echo "</select>";
	echo "Start: <input type='date' id='start_date' style='width:70%'>";
	echo "</br>";
	echo "End: <input type='date' id='end_date' style='width:70%'>";
	echo "</br>";

//echo "<form action='upload.php' method='post' enctype='multipart/form-data'>";
//echo "Select image to upload:";
	echo "<div class='upload-btn-wrapper'>";
	echo "<button class='btn'>Select a File</button>";
	echo "<input type='file' name='fileToUpload' id='fileToUpload' class='submit'>";
	echo "</div>";
//Probably wont need this button
	echo "</br>";
	echo "</br>";
        echo "<div class='submit'>";
        echo "<input id='config_input_button' type='button' name='check-config' value='UPLOAD' onclick='submission();' class='login-button' style='width: 90%;padding: 3%;font-size: 20px;' >";
	echo "<h1 class='feedback' id='feedback-conf' style='font:unset !important; color:red; font-weight: bold !important;'></h1><br>";
        echo "<div align='center' class='copy-right'>";
	echo "<p><a href='http://www.mjns.it' target='_blank'>MJ Network Solutions</a></p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
	echo "<script src='../js/core.min.js'></script>";
	echo "<script src='../js/fsap-login.js'></script>";
        echo "</body>";
        echo "</html>";
    }
}

$application = new OneFileLoginApplication();
