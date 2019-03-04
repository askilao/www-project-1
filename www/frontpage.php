<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();
$video = new Video($db);
$user = new User($db);
$data = $video->getAllVideos();
$path = 'uploadedFiles/';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

// dunno what this is
//$sql = 'SELECT video_id, title, size, description,content FROM video ORDER BY video_id';
$sql = 'SELECT * FROM video ORDER BY video_id';

$sth = $db->prepare ($sql);
$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_ASSOC);


$videos = $video->getAllVideos();
echo $twig->render('frontpage.html', array('data' => $data, 'videos' => $videos));
