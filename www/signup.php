<?php
ob_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./twig/vendor/autoload.php";

$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = DB::getDBConnection();

// If db cant be connected, throw error
if ($db==null) {
    echo $twig->render('nodb.html', array('errmessage' => 'Error: no DB connection'));
    die();
} 
//signup page
 if (!isset($_POST['mail'])) {
        echo $twig->render('signup.html', array());
        exit();
    }

$user = new User($db);
/*
* Lets a person create a new user on the site
*/

if(isset($_POST['mail'])) {
	$userdata['name'] = $_POST['name'];
	$userdata['mail'] = $_POST['mail'];
	$userdata['password'] = $_POST['password'];
	$res = $user->addUser($userdata);

    /*
    * If checked, admin will be notified on login with a user requesting teacher identity
    */

	if(isset($_POST['teacher'])) {	
		$id = $res['id'];
		$user->requestTeacher($id);
	}
	if ($res['status'] == 'OK') {
		$status = $res['status'];
		header("Location: index.php?status={$status}");
	} else {
		echo $twig->render('signup.html', array('error' => $res));
		exit();
	}
}


?>