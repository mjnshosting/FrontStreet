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
        } else if (isset($_GET["action"]) && $_GET["action"] == "manage") {
            $this->showPageManage();
        } else if (isset($_GET["action"]) && $_GET["action"] == "upload") {
            $this->fileUpload();
        } else if (isset($_GET["action"]) && $_GET["action"] == "edit") {
            $this->showEdit();
        } else if (isset($_GET["action"]) && $_GET["action"] == "save") {
            $this->saveEdit();
        } else if (isset($_GET["action"]) && $_GET["action"] == "remove") {
            $this->removeUpload();
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

    private function generateRandomString($length = 16)
    {
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
    	$charactersLength = strlen($characters);
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
        	$randomString .= $characters[rand(0, $charactersLength - 1)];
    	}
	return $randomString;
    }

    private function fileUpload() {
	session_start();
	if (empty($_SESSION['user_name'])) {
	    header('Location: index.php');
	    exit;
	}
        $content_type = $_POST['content_type'];
        $ad_type = $_POST['ad_type'];
        $duration = $_POST['duration'];
        $start_date = strtotime($_POST['start_date']);
        $end_date = strtotime($_POST['end_date']);
	//Randomize file names
	$rangen_name = $this->generateRandomString() . "." . strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
	$target_dir = "../content/";
	$target_file = $target_dir . $rangen_name;
	$uploadOk = 1;
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

	if(isset($_POST["submit"])) {
    		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    		if($check !== false) {
        		echo "File is an image - " . $check["mime"] . ".";
        		$uploadOk = 1;
    		} else {
        		echo "File is not an image.";
        		$uploadOk = 0;
    		}
	}

	//Not needed since file names are random but I will keep it around.
	if (file_exists($target_file)) {
    		echo "Sorry, file already exists.";
    		$uploadOk = 0;
	}

	if ($_FILES["fileToUpload"]["size"] > 10000000) {
    		echo "Sorry, your file is too large. </br> Max file size is 10MBs";
    		$uploadOk = 0;
	}

    	if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
    	&& $fileType != "gif" && $fileType != "webm" && $fileType != "avi"
    	&& $fileType != "flv" && $fileType != "wmv" && $fileType != "mp4"
    	&& $fileType != "ogv" && $fileType != "ogg" && $fileType != "mpg"
    	&& $fileType != "mpeg" ) {
        	echo "Only JPG, JPEG, PNG, GIF, WEBM, AVI, FLV, WMV, MP4, MPG, MPEG, OGV, & OGG files are allowed.";
            	$uploadOk = 0;
	}

	if ($uploadOk == 0) {
    		echo "Sorry, your file was not uploaded.";
	} else {
		if ($this->createDatabaseConnection()) {
			if (!file_exists($target_dir)) {
			    mkdir($target_dir, 0755, true);
			}
	    		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$sql = 'INSERT INTO sliders (content_type, ad_type, duration, start_date, end_date, content)
        		                VALUES(:content_type, :ad_type, :duration, :start_date, :end_date, :content)';
	        	        $query = $this->db_connection->prepare($sql);
	                	$query->bindValue(':content_type', $content_type);
		                $query->bindValue(':ad_type', $ad_type);
		                $query->bindValue(':duration', $duration);
	        	        $query->bindValue(':start_date', $start_date);
		                $query->bindValue(':end_date', $end_date);
		                $query->bindValue(':content', $rangen_name);
				$query->execute();

				echo "Your Ad has been uploaded.";
	    		} else {
        			echo "Sorry, there was an error uploading your file.";
    			}
            	}
	}
    }



    private function showEdit() {
	session_start();
	if (empty($_SESSION['user_name'])) {
	    header('Location: index.php');
	    exit;
	}
	$id = $_POST['id'];
	if ($this->createDatabaseConnection()) {
		//Search for file name
    		$content_sql = "SELECT * FROM sliders WHERE sliders_id=" . $id;
	        $query = $this->db_connection->prepare($content_sql);
		$query->execute();

		//Delete file
		foreach($query as $row) {
			echo "<td style='padding: 5px; vertical-align: middle;'><img src='../content/" . $row['content'] . "' style='max-width: 90%;'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='content-td-" . $row['sliders_id'] . "'><select id='content_type-" . $row['sliders_id'] . "' name='content_type' style='font-size: 12px; width: 45px;'><option value='" . $row['content_type'] . "'>" . ucfirst($row['content_type']) . "</option><option value='image'>Image</option><option value='video'>Video</option></select></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='ad-td-" . $row['sliders_id'] . "'><select id='ad_type-" . $row['sliders_id'] . "' name='ad_type' style='font-size: 12px; width: 45px;'><option value='" . $row['ad_type'] . "'>" . ucfirst($row['ad_type']) . "</option><option value='tenant'>Tenant</option><option value='business'>Business</option><option value='sale'>Sale</option><option value='announcement'>Announcement</option></select></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='duration-td-" . $row['sliders_id'] . "'><input type='text' id='duration-" . $row['sliders_id'] . "' name='duration' value='" . $row['duration'] . "' style='font-size: 12px; width: 45px;'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='start-td-" . $row['sliders_id'] . "'><input type='date' id='start_date-" . $row['sliders_id'] . "' name='start_date' style='font-size: 12px; width: 130px;' value='" . date('Y-m-d', $row['start_date']) . "'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='end-td-" . $row['sliders_id'] . "'><input type='date' id='end_date-" . $row['sliders_id'] . "' name='end_date' style='font-size: 12px; width: 130px;' value='" . date('Y-m-d', $row['end_date']) . "'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='feedback-td-" . $row['sliders_id'] . "'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='toggle-td-" . $row['sliders_id'] . "'><input type='button' value='SAVE' class='save-button' onclick='save_ad(" . $row['sliders_id'] . ")'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;'><input type='button' value='DELETE' class='delete-button' onclick='delete_ad(" . $row['sliders_id'] . ")'></td>";
		}
    	} else {
		echo "Sorry, there was an error.";
	}
    }

    private function saveEdit() {
	session_start();
	if (empty($_SESSION['user_name'])) {
	    header('Location: index.php');
	    exit;
	}
	$sliders_id = $_POST['id'];
        $content_type = $_POST['content_type'];
        $ad_type = $_POST['ad_type'];
        $duration = $_POST['duration'];
        $start_date = strtotime($_POST['start_date']);
        $end_date = strtotime($_POST['end_date']);

        if ($this->createDatabaseConnection()) {
		//Update record
	       	$write = "UPDATE sliders SET content_type = '" . $content_type . "', ad_type = '" . $ad_type . "', duration = '" . $duration . "', start_date = '" . $start_date . "', end_date = '" . $end_date . "' WHERE sliders_id = " . $sliders_id . "";
                $query = $this->db_connection->prepare($write);
                $query->execute();
		//Get updated record
    		$read = "SELECT * FROM sliders WHERE sliders_id = " . $sliders_id . "";
	        $query = $this->db_connection->prepare($read);
                $query->execute();

		foreach($query as $row) {
			echo "<td style='padding: 5px; vertical-align: middle;'><img src='../content/" . $row['content'] . "' style='max-width: 90%;'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='content-td-" . $row['sliders_id'] . "'>" . ucfirst($row['content_type']) . "</td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='ad-td-" . $row['sliders_id'] . "'>" . ucfirst($row['ad_type']) . "</td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='duration-td-" . $row['sliders_id'] . "'>" . $row['duration'] . "s</td>";
			if (time() >= $row['start_date'] && time() <= $row['end_date']) {
				echo "<td style='padding: 10px; vertical-align: middle; color: green; font-weight: bold;' id='start-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['start_date']) . "</td>";
				echo "<td style='padding: 10px; vertical-align: middle; color: green; font-weight: bold;' id='end-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['end_date']) . "</td>";
			} else {
				echo "<td style='padding: 10px; vertical-align: middle; color: red; font-weight: bold;' id='start-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['start_date']) . "</td>";
				echo "<td style='padding: 10px; vertical-align: middle; color: red; font-weight: bold;' id='end-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['end_date']) . "</td>";
			}
			echo "<td style='padding: 10px; vertical-align: middle; color: green;' id='feedback-td-" . $row['sliders_id'] . "'>Saved</td>";
			echo "<td style='padding: 10px; vertical-align: middle;' id='toggle-td-" . $row['sliders_id'] . "'><input type='button' value='EDIT' class='edit-button' onclick='edit_ad(" . $row['sliders_id'] . ")'></td>";
			echo "<td style='padding: 10px; vertical-align: middle;'><input type='button' value='DELETE' class='delete-button' onclick='delete_ad(" . $row['sliders_id'] . ")'></td>";
		}
        } else {
        	echo "Connection Error";
        }
    }

    private function removeUpload() {
	session_start();
	if (empty($_SESSION['user_name'])) {
	    header('Location: index.php');
	    exit;
	}
	$id = $_POST['id'];
	if ($this->createDatabaseConnection()) {
		//Search for file name
    		$content_sql = "SELECT content FROM sliders WHERE sliders_id=" . $id;
	        $query = $this->db_connection->prepare($content_sql);
		$query->execute();

		//Delete file
		foreach($query as $row) {
			unlink('../content/'.$row['content']);
		}

		//Delete DB entry
    		$delete_sql = "DELETE FROM sliders WHERE sliders_id=" . $id;
	        $query = $this->db_connection->prepare($delete_sql);
		$query->execute();
    	} else {
		echo "Sorry, there was an error.";
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
	echo "<script src='../js/jquery.min.js'></script>";
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
	echo "<script src='../js/jquery.min.js'></script>";
	echo "<script src='../js/fsap-login.js'></script>";
        echo "</body>";
        echo "</html>";
    }

    private function showPageSubmission()
    {
	session_start();
	if (empty($_SESSION['user_name'])) {
	    header('Location: index.php');
	    exit;
	}
        echo "<html>";
        echo "<head>";
        echo "<title>Ad Submission</title>";
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
        echo "<h1>Ad Submission</h1>";
        echo "<div align='center' style='width: 70%; margin: 0 auto;'>";
	echo "<form id='uploadForm'>";
        echo "<select id='content_type' name='content_type'>";
	echo "<option value=''>Select Content Type</option>";
	echo "<option value='image'>Image</option>";
	echo "<option value='video'>Video</option>";
        echo "</select>";
	echo "</br>";
        echo "<select id='ad_type' name='ad_type'>";
	echo "<option value=''>Select Ad Type</option>";
	echo "<option value='tenant'>Tenant</option>";
	echo "<option value='business'>Business</option>";
	echo "<option value='sale'>Sale</option>";
	echo "<option value='announcement'>Announcement</option>";
        echo "</select>";
	echo "<div align='right' style='width:100%'>";
	echo "Duration: <input type='text' id='duration' name='duration' placeholder='min 10s/max 30s' style='width:65%'>";
	echo "</br>";
	echo "Start: <input type='date' id='start_date' name='start_date' style='width:80%'>";
	echo "</br>";
	echo "End: <input type='date' id='end_date' name='end_date' style='width:80%'>";
	echo "</br>";
	echo "</div>";
	echo "<div class='upload-btn-wrapper'>";
	echo "<button class='btn'>Select a File</button>";
	echo "<input type='file' name='fileToUpload' id='fileToUpload' class='submit'>";
	echo "</div>";
	echo "</br>";
	echo "</br>";
        echo "<div class='submit'>";
        echo "<input id='config_input_button' type='submit' name='check-config' value='UPLOAD' class='login-button' style='width: 90%;padding: 3%;font-size: 20px;' >";
	echo "</form>";
	echo "<h1 class='feedback' id='feedback-conf' style='font:unset !important; color:red; font-weight: bold !important;'></h1><br>";
        echo "<div align='center' class='copy-right'>";
	echo "<p><a href='index.php?action=manage' id='manage'>Manage Ads</a>";
	echo " &#8226; <a href='index.php?action=logout' id='logout'>Logout</a></p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
	echo "<script src='../js/jquery.min.js'></script>";
	echo "<script src='../js/fsap-login.js'></script>";
        echo "</body>";
        echo "</html>";
    }

    private function showPageManage()
    {
	session_start();
	if (empty($_SESSION['user_name'])) {
	    header('Location: index.php');
	    exit;
	}

	if ($this->createDatabaseConnection()) {
    		$sql = 'SELECT * FROM sliders';
	        $query = $this->db_connection->prepare($sql);
		$query->execute();
    	} else {
		echo "Sorry, there was an error";
	}

        echo "<html>";
        echo "<head>";
        echo "<title>Ad Management</title>";
        echo "<meta charset='utf-8'>";
	echo "<link rel='icon' href='../images/favicon.ico' type='image/x-icon'>";
        echo "<link href='../css/login.css' rel='stylesheet' type='text/css' />";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
        echo "<script type='application/x-javascript'> addEventListener('load', function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>";
	echo "<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lobster:200,300,400,600,700&amp;lang=en'>";
        echo "</head>";
        echo "<body>";
        echo "<div class='main'>";
        echo "<div class='login-form' style='width: 65%;'>";
	echo "<div align='center'><img src='../images/logo.png' alt='Front Street' style='width:128px;height:auto;padding:10px;'></div>";
        echo "<h1>Ad Management</h1>";
        echo "<div align='center' style='width: 85%; margin: 0 auto;'>";
	echo "</br>";
	echo "<div style='overflow-x:auto; overflow-y:auto;' id='manage-list'>";
	echo "<table>";
	echo "<tr style='text-align:center'>";
	echo "<th>Image</th>";
	echo "<th>Content</th>";
	echo "<th>Ad</th>";
	echo "<th>Duration</th>";
	echo "<th>Start</th>";
	echo "<th>End</th>";
	echo "<th></th>";
	echo "<th></th>";
	echo "<th></th>";
	echo "</tr>";

	foreach($query as $row) {
		echo "<tr id='tr-" . $row['sliders_id'] . "' style='text-align:center'>";
		echo "<td style='padding: 5px; vertical-align: middle;'><img src='../content/" . $row['content'] . "' style='max-width: 90%;'></td>";
		echo "<td style='padding: 10px; vertical-align: middle;' id='content-td-" . $row['sliders_id'] . "'>" . ucfirst($row['content_type']) . "</td>";
		echo "<td style='padding: 10px; vertical-align: middle;' id='ad-td-" . $row['sliders_id'] . "'>" . ucfirst($row['ad_type']) . "</td>";
		echo "<td style='padding: 10px; vertical-align: middle;' id='duration-td-" . $row['sliders_id'] . "'>" . $row['duration'] . "s</td>";
		if (time() >= $row['start_date'] && time() <= $row['end_date']) {
			echo "<td style='padding: 10px; vertical-align: middle; color: green; font-weight: bold;' id='start-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['start_date']) . "</td>";
			echo "<td style='padding: 10px; vertical-align: middle; color: green; font-weight: bold;' id='end-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['end_date']) . "</td>";
		} else {
			echo "<td style='padding: 10px; vertical-align: middle; color: red; font-weight: bold;' id='start-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['start_date']) . "</td>";
			echo "<td style='padding: 10px; vertical-align: middle; color: red; font-weight: bold;' id='end-td-" . $row['sliders_id'] . "'>" . date('M/d/Y', $row['end_date']) . "</td>";
		}
		echo "<td style='padding: 10px; vertical-align: middle;' id='feedback-td-" . $row['sliders_id'] . "'></td>";
		echo "<td style='padding: 10px; vertical-align: middle;' id='toggle-td-" . $row['sliders_id'] . "'><input type='button' value='EDIT' class='edit-button' onclick='edit_ad(" . $row['sliders_id'] . ")'></td>";
		echo "<td style='padding: 10px; vertical-align: middle;'><input type='button' value='DELETE' class='delete-button' onclick='delete_ad(" . $row['sliders_id'] . ")'></td>";
		echo "</tr>";
	}

	echo "</table>";
	echo "</div>";

	echo "<h1 class='feedback' id='feedback-conf' style='font:unset !important; color:red; font-weight: bold !important;'></h1><br>";

	echo "</br>";
        echo "<div align='center' class='copy-right' style='padding-bottom: 4.5%;'>";
	echo "<p><a href='index.php?action=submission' id='logout'>Submit Ad</a>";
	echo " &#8226; <a href='index.php?action=logout' id='logout'>Logout</a></p>";
        echo "</div>";

        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
	echo "<script src='../js/jquery.min.js'></script>";
	echo "<script src='../js/fsap-login.js'></script>";
        echo "</body>";
        echo "</html>";
    }
}

$application = new OneFileLoginApplication();
