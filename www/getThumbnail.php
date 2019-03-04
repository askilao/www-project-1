<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();


/*
* Fetches thumbnails from database to display when a user browses videos
*/


if (isset($_GET['id'])) {
    $sql = "SELECT thumbnail FROM video WHERE video_id=?";
    $sth = $db->prepare ($sql);
    $id = $_GET['id'];
    $sth->execute(array($id));
    if ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
      header("Content-type: image/png");
      echo $row['thumbnail'];
    } else {
      header("HTTP/1.0 404 Not Found");
    }
}
?>

