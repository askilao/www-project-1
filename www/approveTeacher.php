<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Admin.php";

$db = DB::getDBConnection();
$user = new User($db);
$admin = new Admin($db);


/*
* Function for admin to approve pending user requests, requesting teacher status for their user
*/

if ($user->isAdmin()) {
    //if accepted send requests to function
    if (isset($_POST['accept'])) {
        if (isset($_POST['teacherCheck'])) {
            $requests = $_POST['teacherCheck'];
            $admin->approve($requests);
            header("Location: admin.php");
        }
    // if rejected then reject
    } elseif (isset($_POST['reject'])) {
            $requests = $_POST['teacherCheck'];
            $admin->reject($requests);
            header("Location: admin.php");
        }
} 
?>