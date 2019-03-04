<?php
class Admin extends User {
    private $db;


    public function __construct($db){
      $this->db = $db;
  }
  /*Check if a user has role 2 
  * @param id of admin or user
  * @return boolean
  */
public function isSuperAdmin($admin_id){
    $sql = 'SELECT role, isAdmin FROM user WHERE id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($admin_id)); 
    if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      //Admin has to be both role 2 (main admin) and isAdmin must be true
      if ($row['role'] == '2' && $row['isAdmin'] == '1'){
        return true;
      } else {
        return false;
      }
    }
    return false;
}
  /*Get all current teacher requests from database
  * @param 
  * @return row of user ids and mails of teacher requests
  */
  public function getTeacherRequests() {
    $sql = 'SELECT mail, id FROM user WHERE id IN (SELECT user_id FROM teacher_request)';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ());
      if ($row = $sth->fetchAll()) {
         return $row;
      }

  }
  /*Get all current admins, is used in admin controlpanel
  * @param 
  * @return row of data for all admins
  */
  public function getAdmins() {
    $sql = 'SELECT id, mail, firstname, role FROM user WHERE isAdmin=1 ORDER BY role DESC';
    $sth = $this->db->prepare($sql);
    $sth-> execute(array());
      if ($row = $sth->fetchAll()) {
         return $row;
      }
  } 
  /*Remove an admin
  * @param user id of admin
  * @return status array
  */
  public function removeAdmin($admin_id) {
    $sql = "UPDATE user SET isAdmin='0' WHERE user.id=?";
    $sth = $this->db->prepare($sql);
    $sth-> execute(array($admin_id));   
      if ($sth->rowCount()==1) {
          $tmp['status'] = 'OK';
      } else {
          $tmp['status'] = 'FAIL';
      }
    return $tmp; 

  }
  /*Make user with isAdmin = 0 an admin (isAdmin = 1)
  * @param user id of user to promote
  * @return status array
  */
  public function promoteUser($user_id) {
    $sql = "UPDATE user SET isAdmin='1' WHERE user.id=?";
    $sth = $this->db->prepare($sql);
    $sth-> execute(array($user_id));   
      if ($sth->rowCount()==1) {
          $tmp['status'] = 'OK';
      } else {
          $tmp['status'] = 'FAIL';
      }
    return $tmp; 
  }

  /*Approve teacher requests on admin control panel
  * Goes through all requests and updates table, 
  * then deletes from request table.
  * @param array of teacher requests to approve
  * @return status array
  */
  public function approve($requests) {
    $sql = "UPDATE user SET role='1' WHERE user.id=?";
    $sth = $this->db->prepare($sql);
    foreach ($requests as $teacher_id) {
      $sth-> execute(array($teacher_id));
      if ($sth->rowCount()==1) {
          $this->deleteFromRequest($teacher_id);
      } else {
          //Site refreshes after approve is done, not sure how to pass data
          echo 'fail';
      }
    }
  }
  /*Opposite of approve, goes through request array
  * and calls delete function.
  * @param array of requests to delete
  * @return 
  */
  public function reject($requests) {
    foreach ($requests as $teacher_id) {
        $this->deleteFromRequest($teacher_id);
  }
}
  /*Deletes requested id from teacher request table
  * @param array of requests to delete
  * @return 
  */
  private function deleteFromRequest($teacher_id) {
      $sql = "DELETE FROM teacher_request WHERE user_id=?";
      $sth = $this->db->prepare($sql);
      $sth-> execute (array($teacher_id));
  }
  /*Search after user based on keyword
  * @param keyword to match a column in user table
  * @return row of matched users
  */
  public function getUsersMatching($keyword) {
    $sql = 'SELECT id, firstname, mail, role, isAdmin FROM user WHERE firstname LIKE ? OR mail LIKE ? OR role LIKE ?';
    $sth = $this->db->prepare($sql);
    $sth-> execute(array("%$keyword%", "%$keyword%", "%$keyword%"));
     if ($row = $sth->fetchAll()) {
       return $row;
    }

  }
  /*Returns all users in table, used when keyword is empty in search
  * @param 
  * @return row with userdata of all users in user table
  */
  public function getAllUsers() {
    $sql = 'SELECT id, firstname, mail, role, isAdmin FROM user';
    $sth = $this->db->prepare($sql);
    $sth-> execute(array());
     if ($row = $sth->fetchAll()) {
       return $row;
    }
  }


  }


?>