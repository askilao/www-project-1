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
$playlist = new Playlist($db);


/*
* Shows the video clicked on by a user,
* fetches other videos by same teacher/uploader
* if variable id actually has video content
*/

//if video is to be seen

if (!empty($_GET['id'])) {

  $id = $_GET['id'];
  //get other related videos that has the same subject code
  $otherVideos = $video->getVideosBySubject($id);
  //display video on page
  displayVideo($id, $video, $user, $otherVideos, $playlistInfo = false);
 
 // if playlist is to be seen
} elseif (isset($_GET['playlist_id'])) {
	$playlist_id = $_GET['playlist_id'];
	//get all videos in playlist
	$playlist_videos = $playlist->getPlaylistVideos($playlist_id);
	//get info on playlist
	$playlistInfo = $playlist->getPlaylist($playlist_id);

	//First video to play when clicking on playlist is the first in the order
	$video_id = $playlist_videos[0]['video_id'];
	$playlist_videos = getVideotitle($playlist_videos, $video);
	//rest is sent with function and displayed
	displayVideo($video_id, $video, $user, $playlist_videos, $playlistInfo);
} else {
	echo 'Nothing to show';
}

/*
* Shows a video with all the specific data (teacher/uploaders name, rating etc.) related to the video
*/
function displayVideo($id, $video, $user, $otherVideos, $playlistInfo) {
	$userdata = $user->getData();
	$path = 'uploadedFiles/';
	$loader = new Twig_Loader_Filesystem('views');
	$twig = new Twig_Environment($loader, array());

	//get video info
	$videoInfo = $video->getVideo($id);
	$teacher_id =  $videoInfo['fk_owner_id'];
	//get info on the creator of the video

	$teacherInfo = $video->getOwnerById($teacher_id);
	//get video path to render on page
	$videoInfo['path'] = $path . $videoInfo['video_id'] . "_" . $videoInfo['filename'];
	//get comments of the video
	$comments = $video->getComments($id);
	//get rating of video
	$rating= generateRating($video->getRating($id));
	//if user has rated then dont show rating field
	$userdata['hasRated'] = $user->hasRated($id);
	echo $twig->render('showVideo.html', array('videoInfo' => $videoInfo, 'userdata' => $userdata, 'teacher' => $teacherInfo, 'comments' => $comments, 'ratings' => $rating, 'otherVideos' => $otherVideos, 'playlistInfo' => $playlistInfo));

}

/* Rating is generated so that
* up until the rating of the video
* the id of the element is sat as filled-star or empty star
*css will then render the correct color of the star
>>>>>>> Stashed changes
*/
function generateRating($rating) {
	$count = 1;
	$result = [];

	for($i = 1; $i <= 5; $i++){
	    if($rating >= $count){
	        $result[$i] = "filled-star";
	    } else {
	        $result[$i] = "empty-star";
	    }
	    $count++;
	}
	return $result;

}

/*
* Fetches the title of a video by the video_id
*/

function getVideoTitle($videos, $video) {

    foreach ($videos as $key => $playlist_video) {
        $videos[$key]['title'] = $video->getVideoTitle($playlist_video['video_id']);
    }
    return $videos;
}
?>