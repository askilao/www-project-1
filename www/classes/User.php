<?php
class User {
	private $db;
  private $uid = -1;
	private $userdata = [];
	//Users permissions, 0 is regular, 1 is teacher, 2 is admin
	protected $role = 0;
  protected $isAdmin = 0;
  private $rememberMe = 0;

	public function __construct($db){
		$this->db = $db;

    //if mail is set in login page, call login function

    if (isset($_POST['mail'])) {
      $logindata['mail'] = $_POST['mail'];
      $logindata['password'] = $_POST['password'];
      //If rememberMe is set on login, set to true
      if (isset($_POST['rememberMe'])) {
        $this->rememberMe = true;
      }
      $this->login($logindata, $this->rememberMe);
      } else if (isset($_POST['logout'])) {
        // when user logs out, delete cookie and destroy session
        setcookie('rememberMe', '',time() - 3600);
        $this->deleteCookie();
        session_destroy();
        //if user is logged in, get session data
      } else if (isset($_SESSION['user'])) {
        $this->uid = $_SESSION['user']['uid'];
        $this->role = $_SESSION['user']['role'];
        $this->isAdmin = $_SESSION['user']['isAdmin'];

      }

	}
  /*Check if user is logged in based on user id
  * @param 
  * @return boolean
  */
  public function loggedIn() {
    return $this->uid>=1;
  }
  /*Get role of user
  * @param
  * @return int 0, 1, or 2 (student, teacher, admin)
  */
  public function getRole() {
    return $this->role;
  }
  /*Check if user is admin
  * @param 
  * @return boolean
  */
    public function isAdmin() {
    return $this->isAdmin;
  }
  /*Get user id
  * @param 
  * @return id of user
  */
  public function getUid() {
	    return $this->uid;
  }
  /*Get data of user
  * @param playlist id
  * @return row of all the data of this user
  */
  public function getData() {
    $sql = 'SELECT * FROM user WHERE id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute(array($this->uid));

    if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      return $row;
    }
  }
  /*Get mail of this user
  * used when adding comment
  * @param 
  * @return mail of this user
  */
   public function getMail() {
    $sql = 'SELECT mail FROM user WHERE id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute(array($this->uid));

    if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      return $row['mail'];
    }
  }
  /*Get name of teacher based on id
  * @param teacher id
  * @return firstname (actually the whole name) of teacher
  */
  public function getTeacherName($teacher_id) {
    $sql = 'SELECT firstname FROM user WHERE id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute(array($teacher_id));
     if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      return $row['firstname'];
    }
  }

  /*Delete cookie on logout
  * @param
  * @return
  */
  private function deleteCookie() {
    $sql = 'DELETE * FROM auth_token WHERE user_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($this->uid));
  }

  /*Add user to database
  * @param userdata array
  * @return status array
  */
 private function checkMail($mail) {
    $sql = 'SELECT id FROM user WHERE mail=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($mail));
    if ($sth->rowCount()) {
      return true;
    } else {
      return false;
    }
  }
  /*Log that user has rated on a given video
  * @param video id
  * @return
  */
  public function log_rating($video_id) {
    $sql = 'INSERT INTO rating_log (user_id, video_id) VALUE (?, ?)';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($this->uid, $video_id));
  }
  /*Check if user has already rated on a video
  * @param video id
  * @return boolean
  */
  public function hasRated($video_id) {
    $sql = 'SELECT id FROM rating_log WHERE user_id=? AND video_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($this->uid, $video_id));
    if ($sth->rowCount()==1) {
      return true;
    } else {
      return false;
    }
  }
  /*Get playlists that user subscribes to
  * @param 
  * @return row of playlist ids
  */
  public function getSubscribedPlaylists() {
    $sql = 'SELECT playlist_id FROM playlist_subscriptions WHERE user_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($this->uid));
    if ($row = $sth->fetchAll()) {
            return $row;
        }
  }

  /*Check if user already has subscribed to playlist
  * @param playlist id
  * @return boolean
  */
  public function hasSubscribed($playlist_id) {
    $sql = 'SELECT playlist_id FROM playlist_subscriptions WHERE user_id=? AND playlist_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($this->uid, $playlist_id));
    if ($sth->rowCount()==1) {
      return true;
    } else {
      return false;
    }
  }
  /*Remove playlist for a given id in subscription table
  * @param playlist id
  * @return boolean 
  */
  public function unsubscribe($playlist_id) {
    $sql = 'DELETE FROM playlist_subscriptions WHERE user_id=? AND playlist_id=?';
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($this->uid, $playlist_id));
    print_r($sth->errorInfo());
    if ($sth->rowCount()==1) {
      return true;
    } else {
      return false;
    }
  }

	/*Add user to database
	* @param userdata array
	* @return status array
	*/
	public function addUser($userdata) {
		// New user is regular user until apporved as admin.
		$this->role = 0;
		$sql = 'INSERT INTO user (firstname, mail, password, role, isAdmin) VALUES (?, ?, ?, ?, ?)';
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$sth = $this->db->prepare($sql);
    if (!$this->checkMail($userdata['mail'])) {
		  //Fill in sql query with userdata, password is hashed
  		$sth->execute(array($userdata['name'], $userdata['mail'], password_hash($userdata['password'], PASSWORD_DEFAULT), $this->role, $this->isAdmin));
      print_r($sth->errorInfo());
  		//Return if successful or not, id is auto incremented
  		if ($sth->rowCount()==1) {
        		$tmp['status'] = 'OK';
        		$tmp['id'] = $this->db->lastInsertId();
      	} else {
        		$tmp['status'] = 'FAIL';
        		$tmp['errorMessage'] = 'Could not create user';
      	}
      	// Error in SQL
      	if ($this->db->errorInfo()[1]!=0) { 
        		$tmp['errorMessage'] = $this->db->errorInfo()[2];
      	}
      } else {
        $tmp['status'] = 'FAIL';
        $tmp['errorMessage'] = $userdata['mail'] . ' already taken' ;
      }
    	return $tmp; 

	}

	/* Delete user from database
	 * @param userID
	 * @return status array
	*/
	public function deleteUser() {
        $sql = "delete from user where id =?";
        $sth = $this->db->prepare($sql);
    	$sth-> execute(array($this->uid));
     	
     	if ($sth->rowCount()==1) {
      		$tmp['status'] = 'OK';
    	} else {
      		$tmp['status'] = 'FAIL';
    	}

    	return $tmp; 

	}
	/* Request to become teacher
	 * @param user_id
	 * @return status array
	*/
	public function requestTeacher($id) {
		$sql = 'INSERT INTO teacher_request (user_id) VALUES (?)';
		$sth = $this->db->prepare($sql);
    $sth-> execute(array($id));
     	
     	if ($sth->rowCount()==1) {
      		$tmp['status'] = 'OK';
    	} else {
      		$tmp['status'] = 'FAIL';
    	}

    	return $tmp; 

	}

  /*Login user
  * @param array of username and password
  * @return status array
  */
	public function login($logindata, $rememberMe) {
    //get data based on username
		$sql = 'SELECT * FROM user WHERE mail=?';
		$sth = $this->db->prepare($sql);
    $sth-> execute (array ($logindata['mail']));
    	if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    		//If Username exists and password is verified
      		if(password_verify($logindata['password'], $row['password'])){
            //if password in correct then set userdata in session 
            $this->userdata = $row;
            $_SESSION['user'] = array(
              'uid' => $row['id'], 
              'role' => $row['role'], 
              'isAdmin' => $row['isAdmin']
            );
            $this->uid = $row['id'];
            $this->role = $row['role'];
            $this->isAdmin = $row['isAdmin'];

      			  /*Create persistent cookie
              * is supposed to work with the best practice for remember me Cookies
              * https://stackoverflow.com/questions/244882/what-is-the-best-way-to-implement-remember-me-for-a-website
              */
      			if ($rememberMe) {
      				// random bytes with large space to give collision resistance
      				$selector = base64_encode(random_bytes(9));
      				$authenticator = random_bytes(33);
      				setcookie(
      					'rememberMe',
      					$selector.':'.base64_encode($authenticator),
      					time() + 864000,
      					'/',
      				);
      				//insert cookie data into table
      				$sql = "INSERT INTO auth_token (selector, token, user_id, role, isAdmin, expires) VALUES (?, ?, ?, ?, ?, ?)";
      				$sth = $this->db->prepare($sql);
      				$sth-> execute(array(
      					$selector,
      					hash('sha256', $authenticator),
      					$row['id'],
                $row['role'],
                $row['isAdmin'].
      					date('Y-m-d\TH:i:s', time() + 864000)
      				));
      			}

        		return array('status'=>'OK');
      		} else {
        		return array('status'=>'FAIL', 'errmsg' => 'Wrong password');
      		}
    	} else {
      		return array('status'=>'FAIL', 'errmsg' => 'User not found');
    	} 
	}

}












?>