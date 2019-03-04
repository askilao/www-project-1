<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();
$video = new Video($db);
$user = new User($db);
$userdata = $user->getData();
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());


/*
* Lets users comment on a video
*/

if (isset($_POST['commentContent'])) {
  //get data to store comment
  $commentData['video_id'] = $_POST['video_id'];
  $commentData['mail'] = $user->getMail();
  $commentData['comment'] = $_POST['commentContent'];
  $commentData['time'] = date("Y-m-d H:i:s");
  $result = $video->postComment($commentData);
  echo $result['status'];
  if($result['status'] == 'OK') {
    // of all is good then page is reloaded
  	header("Location: showVideo.php?id={$commentData['video_id']}");

  } else {
  	header("HTTP/1.0 404 Not Found");
  }
}
?>