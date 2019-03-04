<?php
session_start();

require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./twig/vendor/autoload.php";


$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = DB::getDBConnection();
$user = new User($db);
$video = new Video($db);

//Check if user is logged in and is a teacher
if($user->loggedIn() && $user->getRole() == 1) {

    // If DB can't be connected, throws error
    if ($db==null) {
        echo $twig->render('nodb.html', array('errmessage' => 'Error: no DB connection'));
        die();
    }

    // Upload page if no file was set
    if (!isset($_FILES['fileToUpload'])) {
        echo $twig->render('uploadFile.html', array());
        exit();
    }



    /*
    * Uploads content to SQL database
    */
    if (is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {

        $videoData['mime'] = $_FILES['fileToUpload']['type'];
        $videoData['size'] = $_FILES['fileToUpload']['size']; 
        $videoData['filename'] = strtolower($_FILES['fileToUpload']['name']);  // "movie4.mp4"
        // regex--> "mp4"
        // get video data and send to database
        $videoData['extension'] = strtolower(substr($videoData['filename'], strpos($videoData['filename'], ".")));
        $videoData['title'] = strtolower($_POST['title']);
        $videoData['owner'] = $user->getUid();
        $videoData['description']  = $_POST['descr'];
        $videoData['emne']  = $_POST['emne'];
        $result = $video->newVideo($videoData);

        // Stores file to persistent storage
        if ($result['status'] == 'OK') {
            header("Location: index.php");
        } else {
            //if upload failed
            echo $twig->render('uploadFail.html', array('fname' => $result['filename'], 'msg' => $result['errorMessage']));
        }
    } else {
        //If accessing uploadFile but user is not teacher
        echo $twig->render('nicetry.html', array());
    }
}


?>

