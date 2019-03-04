<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Admin.php";
require_once "./classes/Video.php";
require_once "./twig/vendor/autoload.php";

$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = DB::getDBConnection();

// If db cant be connected, throw error
if ($db==null) {
    echo $twig->render('nodb.html', array('errmessage' => 'Error: no DB connection'));
    die();
} 

// check if session is not set but cookie is
if (empty($_SESSION['uid']) && !empty($_COOKIE['rememberMe'])) {
	list($selector, $authenticator) = explode(":", $_COOKIE['rememberMe']);
  //if so then get auth_token from database 
	$sql = 'SELECT * FROM auth_token WHERE selector=?';
	$sth = $db->prepare($sql);
    $sth-> execute (array ($selector));
    if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      //compare token and authenticator
    	if(hash_equals($row['token'], hash('sha256', base64_decode($authenticator)))) {
        //if match then set user variables
    		$_SESSION['user'] = array(
          'uid' => $row['id'],
          'role' => $row['role'],
          'isAdmin' => $row['isAdmin']
        );

    		// random bytes with large space to give collision resistance
        //regenerate cookie
      		$selector = base64_encode(random_bytes(9));
      		$authenticator = random_bytes(33);
      		setcookie(
      			'userCookie',
      			$selector.':'.base64_encode($authenticator),
      			time() + 864000,
      			'/'
      			);
      		//insert cookie data into table
      		$sql = "INSERT INTO auth_token (selector, token, user_id, expires) VALUES (?, ?, ?, ?)";
      		$sth = $db->prepare($sql);
      		$sth-> execute(array(
      			$selector,
      			hash('sha256', $authenticator),
      			$row['user_id'],
      			date('Y-m-d\TH:i:s', time() + 864000)
      		));
    	}
    }
}

//send user to his or hers page based on role
$user = new User($db);
if(isset($_GET['status'])){
  // if page was referenced by sign up page
    echo $twig->render('index.html', array('status' => 'User created, you can now log in!'));
  } elseif ($user->loggedIn()) {
      $data = $user->getData();
      switch ($data['role']) {
        case 0:
            header("Location: student.php");
          break;
        case 1:
          header("Location: teacher.php");
          break;
        case 2:
         header("Location: admin.php");
          break;
        
        default:
          break;
      }
    } else {
        echo $twig->render('index.html', array());
    }

?>
