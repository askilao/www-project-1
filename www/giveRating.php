<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();
$video = new Video($db);
$user = new User($db);


/*
* Lets users rate uploaded videos
*/

if (isset($_POST['rating'])) {
	$video_id = $_POST['video_id'];
	//you can only rate once, sorry
	if (!$user->hasRated($video_id)) {
		$rating = $_POST['rating'];
		$result = $video->giveRating($video_id, $rating);
		//when rated the system remembers, dont try to fool it
		$user->log_rating($video_id);
	}
}
?>