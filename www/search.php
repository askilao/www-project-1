<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./classes/Admin.php";
require_once "./classes/Playlist.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();
$user = new User($db);
$video = new Video($db);
$playlist = new Playlist($db);

$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

/* Get search results or show all videos and playlists
*/

$data = $user->getData();
$videos = $video->getAllVideos();
$allPlaylists = $playlist->getAllPlaylists();
//get teachernames of ids in playlists
if ($allPlaylists !==NULL) {
    $allPlaylists = getTeacherNames($allPlaylists, $user);
}



/*
* Gets query from user and searches through the database in columns for
* teacher, subject, titles; displays matches if found
*/


//if a search query was set then search in database for it

if(!empty($_GET['q'])) {
    $keyword = $_GET['q'];
    // get videos matching
    $searchMatch['video'] = $video->getVideosMatching($keyword);
    // get playlists containing matching videos
    $searchMatch['playlist'] = $playlist->getPlaylistMatching($keyword);
    //if one isnt empty then show results
     if ($searchMatch['video'] !==NULL || $searchMatch['playlist'] !==NULL) {
;       
        if ($searchMatch['playlist'] !==NULL) {
            $searchMatch['playlist'] = getTeacherNames($searchMatch['playlist'], $user);
        }
        // render page with results
        $search['keyword'] = $keyword;
        echo $twig->render('browse.html', array('userdata' => $data, 'videos' => $searchMatch['video'], 'playlists' => $searchMatch['playlist'], 'search' => $search));
        exit();
        } else {
            //render site with message saying nothing matched search
            $error['message'] = $keyword . ' did not match any results';
            echo $twig->render('browse.html', array('userdata' => $data, 'error' => $error));
            exit();
        }

/*
* Lets user subscribe to a playlist
*/

// if subscribe button is pressed on playlist table
} elseif (isset($_POST['subscribe'])) {
    $playlist_id = $_POST['playlist_id'];
    //cant subscribe twice mister
    if ($user->hasSubscribed($playlist_id) == NULL) {
        //put subscriber in array and render page again
        $playlist->addSubscriber($playlist_id, $user->getUid());
        echo $twig->render('browse.html', array('userdata' => $data, 'videos' => $videos, 'playlists' => $allPlaylists ));
        exit();
    } else {
        //error message if user is already subscribed
        $error['message'] = 'You are already subscribed!';
        echo $twig->render('browse.html', array('userdata' => $data, 'error' => $error, 'videos' => $videos, 'playlists' => $allPlaylists));
        exit();
    }
} else {
    echo $twig->render('browse.html', array('userdata' => $data, 'videos' => $videos, 'playlists' => $allPlaylists ));
    exit();
}

  /*Get names of teacher to present in search
  * @param playlists array and user object
  * @return playlist array with creator names
  */
function getTeacherNames($playlists, $user) {

    foreach ($playlists as $key => $playlist_data) {
        $playlists[$key]['creator'] = $user->getTeacherName($playlist_data['created_by_user']);
    }
    return $playlists;
}



