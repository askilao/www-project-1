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

//gets data and subscribed playlists for logged in user

$user = new User($db);
$playlist = new Playlist($db);


//has to be user
if ($user->getRole() == 0) {
    //if user unsubbed on a playlist

    if(isset($_POST['unsub'])) {
        $playlist_id = $_POST['playlist_id'];
        $result = $user->unsubscribe($playlist_id);
        if($result) {
            header("Location: index.php");
        }
    } else {
        /*
        * Shows the subscribed playlists on the html page
        */
        $data = $user->getData();
        $subscribed_playlists = $user->getSubscribedPlaylists();
        // if user has any subscribed playlist the get data and videos
        if ($subscribed_playlists !=NULL) {
            foreach ($subscribed_playlists as $key => $subscribed_playlist) {
                $subscribed_playlists[$key] = $playlist->getPlaylist($subscribed_playlist['playlist_id']);
                $subscribed_playlists[$key]['videos'] = $playlist->getPlaylistVideos($subscribed_playlist['playlist_id']);
            }
        }
        echo $twig->render('studentFront.html', array('data' => $data,'subscribed_playlists' => $subscribed_playlists, 'playlist_videos' => $subscribed_playlists));
        exit();
    }

} else {
    // if not student
    echo $twig->render('nicetry.html', array());
    exit();
}
?>



