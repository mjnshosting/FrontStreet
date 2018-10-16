<?php
try {
    /**************************************
    * Create databases and                *
    * open connections                    *
    **************************************/
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:8_4o/front_street_advertising_project.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);
    // Create new database in memory
    $memory_db = new PDO('sqlite::memory:');
    // Set errormode to exceptions
    $memory_db->setAttribute(PDO::ATTR_ERRMODE,
                              PDO::ERRMODE_EXCEPTION);
    // Query DB and create mac list text file that will show up in the iFrame
//    $result = $file_db->query('SELECT * FROM sliders');

	echo "<html class='wide wow-animation scrollTo smoothscroll desktop landscape rd-navbar-static-linked' lang='en'>";
	echo "<head>";
	echo "<title></title>";
	echo "<meta charset='utf-8'>";
	echo "<meta name='format-detection' content='telephone=no'>";
	echo "<meta name='viewport' content='width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>";
	echo "<meta http-equiv='X-UA-Compatible' content='IE=Edge'>";
	echo "<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Montserrat:400,700%7CLato:300,300italic,400,700,900%7CYesteryear'>";
	echo "<link rel='stylesheet' href='css/style.css'>";
	echo "</head>";
	echo "<body>";
	echo "<div class='page text-center'>";
	echo "<!-- Page Head-->";
	echo "<header class='section page-head slider-menu-position'>";
	echo "<!--Swiper-->";
	echo "<div class='swiper-container swiper-slider' data-loop='true' data-autoplay='true' data-height='100vh' data-dragable='false' data-min-height='480px'>";
	echo "<div class='swiper-wrapper text-center'>";
	echo "<!--Sliders Div-->";

    // Output Lines
/**
    foreach($result as $row) {
	echo "<!--Slider-->";
    	echo "<div class='swiper-slide' id='page-loader' data-slide-bg='content/" . $row['content']  . "' data-preview-bg='content/" . $row['content']  . "'></div>";
	echo "<!--End Slider-->";
    }
**/

echo "<div class='swiper-slide' id='page-loader' data-slide-bg='content/intro-04-1920x955.jpg' data-preview-bg='content/intro-04-1920x955.jpg'></div>";
echo "<div class='swiper-slide' id='page-loader' data-slide-bg='content/intro-05-1920x955.jpg' data-preview-bg='content/intro-05-1920x955.jpg'></div>";
echo "<div class='swiper-slide' id='page-loader' data-slide-bg='content/intro-06-1920x955.jpg' data-preview-bg='content/intro-06-1920x955.jpg'></div>";

	echo "<!--End Sliders Div-->";
	echo "</div>";
	echo "</div>";
	echo "<!--End Swiper-->";
	echo "</header>";
	echo "</div>";
	echo "<!-- Page Head-->";
	echo "<script src='js/core.min.js'></script>";
	echo "<script src='js/script.js'></script>";
	echo "<script src='js/fsap.js'></script>";
	echo "</body>";
	echo "</html>";

    /**************************************
    * Close db connections                *
    **************************************/
    // Close file db connection
    $file_db = null;
    // Close memory db connection
    $memory_db = null;
  }
  catch(PDOException $e) {
    // Print PDOException smam
    echo $e->getMessage();
  }

?>
