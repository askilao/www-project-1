<?php
session_start();
require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./classes/Admin.php";
require_once "./twig/vendor/autoload.php";

$db = DB::getDBConnection();
$user = new User($db);
$admin = new Admin($db);
$userdata = $user->getData();
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());


if ($user->isAdmin()) {
  $data = $user->getData();
  $teacherRequests = $admin->getTeacherRequests();
  $adminlist = $admin->getAdmins();
  $adminlist = createRoleNames($adminlist);
  /*If teacher request is accepted, all in checkbox will be accepted
  * Page is refreshed
  */
  if (isset($_POST['accept']) && isset($_POST['teacherCheck'])) {
          $requests = $_POST['teacherCheck'];
          $admin->approve($requests);
          header("Location: admin.php");
   
  /*If teacher request is rejected, all checked will be deleted
  * from teacher_request table and page refreshed
  */
  } elseif (isset($_POST['reject']) && isset($_POST['teacherCheck'])) {
          $requests = $_POST['teacherCheck'];
          $admin->reject($requests);
          header("Location: admin.php");
  
  /*In adminlist admins can be removed */
  } elseif (isset($_POST['remove'])) {
      $admin_id = $_POST['remove'];
      /*Check if user is removing itself or a super admin, gives error on page*/
      if ($admin_id == $user->getUid() || $admin->isSuperAdmin($admin_id)){
          $error['message'] = 'Error: tried deleting self or super admin, dont do that...';
          echo $twig->render('adminFront.html', array(
            'data' => $data, 
            'requests' => $teacherRequests, 
            'admins' => $adminlist, 
            'error' => $error ));
          exit();
        } else {
  /*If all is good, then remove and refresh*/
            $result = $admin->removeAdmin($admin_id);
            if ($result['status'] == 'OK') {
              header("Location: admin.php");
            } else {
                $error['message'] = $result['status'];
                echo $twig->render('adminFront.html', array(
                  'data' => $data, 
                  'requests' => $teacherRequests, 
                  'admins' => $adminlist, 
                  'error' => $error ));
                exit();
            } 
        }
  /*Get list of users, give button for promoting to admin*/
  } elseif (isset($_POST['searchBtn'])) {
      if(!empty($_POST['searchUser'])){
        $keyword = $_POST['searchUser'];
        $searchMatch = $admin->getUsersMatching($keyword);
        if ($searchMatch !==NULL) {
          $searchMatch = createRoleNames($searchMatch);
          echo $twig->render('adminFront.html', array(
            'data' => $data, 
            'requests' => $teacherRequests, 
            'admins' => $adminlist, 
            'searchMatchs' => $searchMatch));
          exit();
        } else {
          $error['message'] = $keyword . ' did not match any results';
          echo $twig->render('adminFront.html', array(
            'data' => $data, 
            'requests' => $teacherRequests, 
            'admins' => $adminlist, 
            'error' => $error));
          exit();
        }

      } else {
        $searchMatch = $admin->getAllUsers();
          $searchMatch = createRoleNames($searchMatch);
          echo $twig->render('adminFront.html', array(
            'data' => $data, 
            'requests' => $teacherRequests, 
            'admins' => $adminlist, 
            'searchMatchs' => $searchMatch));
          exit();
      }
    /*Promote user from search to admin*/
  } elseif (isset($_POST['promote'])) {
      $user_id = $_POST['promote'];
      $result = $admin->promoteUser($user_id);
        if ($result['status'] == 'OK') {
            header("Location: admin.php");
        } else {
            $error['message'] = $result['status'];
            echo $twig->render('adminFront.html', array(
              'data' => $data, 
              'requests' => $teacherRequests, 
              'admins' => $adminlist, 
              'error' => $error ));
            exit();
        }
   
  } else {
  /*Default is render admin frontpage as normal*/
      echo $twig->render('adminFront.html', array(
        'data' => $data, 
        'requests' => $teacherRequests, 
        'admins' => $adminlist));
      exit();
    }
  /*If someone tries to access and isnt registered as admin*/
} else {
	echo $twig->render('nicetry.html', array());
  exit();
}

/*Translate role number from table to human readable words*/
function createRoleNames($userlist) {
  foreach ($userlist as $key => $user) {
    if($user['role'] == '1') {
      $userlist[$key]['role_name'] = 'Teacher';
    } elseif ($user['role'] == '2') {
      $userlist[$key]['role_name'] = 'Super Admin';
    } else {
      $userlist[$key]['role_name'] = 'Student';
    }
  }
  return $userlist;
}
?>