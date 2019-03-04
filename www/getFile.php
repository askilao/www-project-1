<?php
session_start();

require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./twig/vendor/autoload.php";

echo $_SESSION['uid'];

$db = DB::getDBConnection();

$sql = "SELECT fk_owner_id, title, filename, extension, mime, size, description FROM video WHERE video_id=?";
$sth = $db->prepare ($sql);
$sth->execute(array($_GET['video_id']));

if ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
    if (file_exists("uploadedFiles/{$row['fkowner_id']}/{$row['video_id']}")) {
        header('Content-type: '.$row['mime']);
        header('Content-Disposition: attachment; filename='.$row['filename']);
        header('Content-Length: ' . $row['size']);
        readfile ("uploadedFiles/{$row['fk_owner_id']}/{$row['video_id']}");
        die();
    }
}


if (isset($_GET['video_id'])) {
    $sql = "SELECT title, mime, content FROM video WHERE video_id=?";
    $sth = $db->prepare ($sql);
    $sth->execute(array($_GET['video_id']));

    if ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
        header('Content-type: image/png');
        header('Content-Disposition: inline; filename='.$row['filename']);
        header('Content-Length: ' . strlen($row['content']));
        echo ($row['content']);
        die();
    }
}

// Not requesting a thumbnail
$sql = "SELECT fk_owner_id, title, filename, extension, mime, size, description FROM video WHERE video_id=?";
$sth = $db->prepare ($sql);
$sth->execute(array($_GET['video_id']));



if ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
    if (file_exists("uploadedFiles/{$row['fk_owner_id']}/{$row['video_id']}")) {
        header('Content-type: '.$row['mime']);
        header('Content-Disposition: attachment; filename='.$row['title']);
        header('Content-Length: ' . $row['size']);
        readfile ("uploadedFiles/{$row['fk_owner_id']}/{$row['video_id']}");
        die();
    }
}
header("HTTP/1.0 404 Not Found");

