<?php
session_start();

require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./twig/vendor/autoload.php";
require_once "./classes/Video.php";
require_once "./classes/Playlist.php";

$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = DB::getDBConnection();

// If db cant be connected, throw error
if ($db==null) {
    echo $twig->render('nodb.html', array('errmessage' => 'Error: no DB connection'));
    die();
}


$user = new User($db);
$video = new Video($db);
$playlist = new Playlist($db);


/*
* Loads if user privilege is 1, i.e user is teacher.
*/

/* all backend for teacher page
*/
//check that user is teacher first of all

if ($user->getRole() == 1) {
    //if teacher unsubs to a playlist
    if(isset($_POST['unsub'])) {
        $playlist_id = $_POST['playlist_id'];
        $result = $user->unsubscribe($playlist_id);
        if($result) {
            header("Location: index.php");
        }
        /*
        * Lets user edit the data for the uploaded videos
        */

    } elseif (isset($_POST['editVideo'])) { 
        $video_id = $_POST['editVideo_id'];
        $videoData['title'] = $_POST['newTitle'];
        $videoData['descr'] = $_POST['newDescr'];
        $videoData['subject'] = $_POST['newSubject'];
   
        $video->editVideo($video_id, $videoData);
        header("Location: index.php");
        // if teacher deletes video get data and send to video class

    } elseif (isset($_POST['deleteVideo'])) {
        $video_id = $_POST['delete_video_id'];
        $result = $video->deleteVideo($video_id);
        // if deletion was successfull
        if ($result['status'] == 'OK') {
            header("Location: index.php");
        } else {
            //throw error page if not successfull
            echo $twig->render('deleteError.html', array(
                'errmessage' => 'Video delete failed, maybe a playlist is using it ? try deleting that first'
            ));
            die();
        }
        
    } // if not button is pressed then render page along with all teacher data
    else {
        /*
        * Renders all content for the teacherpage when simply showing the teacher html page
        */
        $data = $user->getData();
        $videos = $video->getVideos($data['id']);
        $playlists = $playlist->getTeacherPlaylists($data['id']);
        // get teachers subscribed playlists 
        $subscribed_playlists = $user->getSubscribedPlaylists();
        if ($subscribed_playlists !=NULL) {
            foreach ($subscribed_playlists as $key => $subscribed_playlist) {
                $subscribed_playlists[$key] = $playlist->getPlaylist($subscribed_playlist['playlist_id']);
            }
        }
        //userhasvideos determines if a playlist can be made or not

        echo $twig->render('teacherFront.html', array('data' => $data, 'videos' => $videos,'playlists' => $playlists,'subscribed_playlists' => $subscribed_playlists, 'userhasvideos' => ($videos > 0) ?true : false));
        exit();
    }

} else {
    echo $twig->render('nicetry.html', array());
    exit();
}

?>



