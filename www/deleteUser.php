<?php

session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./classes/Playlist.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();
$video = new Video($db);
$user = new User($db);




if($user->loggedIn() ) {
    // Removes all files before removing user from db
    $videos = $video->getVideos($user->getUid());
    if ($videos > 0) {
        foreach ($videos as $vid) {
            // Deletes SQL entry and File on Disk + Removing video from playlist and the users subsbsribedto-list
            $video->deleteVideo($vid['video_id'], $user->getUid());
        }

    }
    $user->deleteUser();
    setcookie('rememberMe', '', time() - 3600);
    session_destroy();


    echo 'Thanks for using us :)';

}


?>
