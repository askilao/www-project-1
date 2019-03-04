<?php
/**
 * Created by PhpStorm.
 * User: andersgj
 * Date: 2/23/19
 * Time: 2:24 PM
 */
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

//if new playlist is to be made and name is set
if (isset($_POST['playlist_name'])) {
    if (!empty($_POST['checkbox'])) {
        //playlist needs to have at least one video

        //send data to class function
        // TODO missing check for checking if playlist_descriptuon exists. if(!empty($_POST['platlist_description'])
        $playlist->createPlaylist($_POST['playlist_name'],$_POST['playlist_description'],$user->getUid());
        $playlist_id = $db->lastInsertId();
        // add videos to playlist
        foreach ($_POST['checkbox'] as $key => $videoid)
        {
            $playlist->addVideoToPlaylist($videoid, $playlist_id, $key);
        }
        header("Location: index.php");
    } else {
        header("Location: index.php");
    }
/* if a playlist is to be edited
* new data is sent to database 
*/
} elseif (isset($_POST['editPlaylist_name'])) {
    if (!empty($_POST['checkbox'])) {
        //again, a video is needed

        $playlist_id = $_POST['editPlaylist_id'];
        $playlistData['title'] = $_POST['editPlaylist_name'];
        $playlistData['descr'] = $_POST['editPlaylist_description'];
        print_r($playlistData);
        $playlist->editPlaylist($playlistData, $playlist_id);
        //send videos to be added to updated playlist
        $videos = $_POST['checkbox'];
        $result = $playlist->editPlaylistVideos($videos, $playlist_id);

        header("Location: index.php");
    } else {
        header("Location: index.php");
    }
//if delete button is pressed on a given playlist
// removed from database
} elseif (isset($_POST['deletePlaylist'])) {
    $playlist_id = $_POST['delete_playlist_id'];
    $result = $playlist->deletePlaylist($playlist_id);
    if ($result['status'] == 'OK') {
        header("Location: index.php");
    } else {
        //if an error is caused
        echo $twig->render('deleteError.html', array(
            'errmessage' => 'Unable to delete playlist ... contact the backend goblins for more help'
        ));
        die(); //pls
    }
        
}









?>



