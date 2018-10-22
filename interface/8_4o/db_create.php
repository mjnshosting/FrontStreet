<?php
function createDB ()
{
	error_reporting(E_ALL);
	$db_type = "sqlite";
	$db_sqlite_path = "front_street_advertising_project.sqlite3";
	$db_connection = new PDO($db_type . ':' . $db_sqlite_path);
	$user_sql = 'CREATE TABLE IF NOT EXISTS `users` (
		`user_id` INTEGER PRIMARY KEY,
		`user_name` varchar(64),
		`user_password_hash` varchar(255));
		';
	$query = $db_connection->prepare($user_sql);
	$query->execute();

	$user_unique_sql = 'CREATE UNIQUE INDEX `user_name_UNIQUE` ON `users` (`user_name` ASC);';
	$query = $db_connection->prepare($user_unique_sql);
	$query->execute();

	$sliders_sql =	'CREATE TABLE IF NOT EXISTS `sliders` (
		`sliders_id` INTEGER PRIMARY KEY,
		`content_type` varchar(255),
		`ad_type` varchar(255),
		`duration` varchar(255),
		`start_date` varchar(255),
		`end_date` varchar(255),
		`content` varchar(255));
		';
	$query = $db_connection->prepare($sliders_sql);
	$query->execute();
}
//createDB();
?>
