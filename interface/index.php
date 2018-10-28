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
    // Query DB to inject sliders
    $result = $file_db->query('SELECT * FROM sliders');
    $count_sliders_query = $file_db->query("SELECT count(*) FROM sliders")->fetch();
    $count_sliders = $count_sliders_query[0];
    $tenant_sliders = $file_db->query("SELECT * FROM sliders WHERE ad_type='tenant'");
    $business_sliders = $file_db->query("SELECT * FROM sliders WHERE ad_type='business'");
    $sale_sliders = $file_db->query("SELECT * FROM sliders WHERE ad_type='sale'");
    $announcement_sliders = $file_db->query("SELECT * FROM sliders WHERE ad_type='announcement'");

	echo "<html class='wide wow-animation scrollTo smoothscroll desktop landscape rd-navbar-static-linked' lang='en'>";
	echo "<head>";
	echo "<title>Front Street Ad Project</title>";
	echo "<meta charset='utf-8'>";
	echo "<link rel='icon' href='../images/favicon.ico' type='image/x-icon'>";
	echo "<meta name='format-detection' content='telephone=no'>";
	echo "<meta name='viewport' content='width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>";
	echo "<meta http-equiv='X-UA-Compatible' content='IE=Edge'>";
//	Use this when adding in dynamic and moving text to the project. For now its just pictures.
//	echo "<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Montserrat:400,700%7CLato:300,300italic,400,700,900%7CYesteryear'>";
	echo "<link rel='stylesheet' href='css/style.uncss.css'>";
	echo "</head>";
	echo "<body>";
	echo "<div class='page text-center'>";
	echo "<!-- Page Head-->";
	echo "<header class='section page-head slider-menu-position'>";
	echo "<!--Swiper-->";
	echo "<div class='swiper-container swiper-slider' data-loop='true' data-autoplay='true' data-height='100vh' data-dragable='false' data-min-height='480px'>";
	echo "<div class='swiper-wrapper text-center'>";
	echo "<!--Sliders Div-->";

	// Output Sliders
	$x = 0;
	//I usually change this while testing. Default: 1000 - convert milliseconds to seconds.
	$slide_duration = 10;
	while ($x <= $count_sliders) {
		foreach ($tenant_sliders as $row) {
    			if (time() >= $row['start_date'] && time() <= $row['end_date'] && $row['ad_type'] == 'tenant') {
				echo "<!--Slider--><div class='swiper-slide' id='page-loader' data-slide-bg='content/" . $row['content'] . "' data-swiper-autoplay='" . $row['duration']*$slide_duration . "'></div><!--End Slider-->";
				break;
    			}
		}
		foreach ($business_sliders as $row) {
			if (time() >= $row['start_date'] && time() <= $row['end_date'] && $row['ad_type'] == 'business') {
				echo "<!--Slider--><div class='swiper-slide' id='page-loader' data-slide-bg='content/" . $row['content'] . "' data-swiper-autoplay='" . $row['duration']*$slide_duration . "'></div><!--End Slider-->";
				break;
    			}
		}
		foreach ($sale_sliders as $row) {
    			if (time() >= $row['start_date'] && time() <= $row['end_date'] && $row['ad_type'] == 'sale') {
				echo "<!--Slider--><div class='swiper-slide' id='page-loader' data-slide-bg='content/" . $row['content'] . "' data-swiper-autoplay='" . $row['duration']*$slide_duration . "'></div><!--End Slider-->";
				break;
    			}
		}
		foreach ($announcement_sliders as $row) {
    			if (time() >= $row['start_date'] && time() <= $row['end_date'] && $row['ad_type'] == 'announcement') {
				echo "<!--Slider--><div class='swiper-slide' id='page-loader' data-slide-bg='content/" . $row['content'] . "' data-swiper-autoplay='" . $row['duration']*$slide_duration . "'></div><!--End Slider-->";
				break;
    			}
		}
		$x++;
	}
	echo "<!--End Sliders Div-->";
//	echo "<div class='bg-vide'><div style='position: absolute; z-index: -1; top: 0px; left: 0px; bottom: 0px; right: 0px; overflow: hidden; background-size: cover; background-color: transparent; background-repeat: no-repeat; background-position: 50% 50%; background-image: none;'><video autoplay='' loop='' muted='' style='margin: auto; position: absolute; z-index: -1; top: 50%; left: 50%; transform: translate(-50%, -50%); visibility: visible; opacity: 1; width: 1905px; height: auto;'><source src='content/xHzcEaJTsbeN4XyE.mp4' type='video/mp4'></video></div>";
	echo "</div>";
	echo "</div>";
	echo "<!--End Swiper-->";
	echo "</header>";
	echo "</div>";
	echo "<!-- Page Head-->";
	echo "<script src='js/jquery.min.js'></script>";
	echo "<script src='js/swiper4.min.js'></script>";
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
