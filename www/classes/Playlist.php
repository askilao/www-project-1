<?php
class Playlist {
    private $db;


    public function __construct($db){
      $this->db = $db;
  }
  
  /*Database debuging:
  *Before prepare funtion: 
  * $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  *After execute
  * print_r($sth->errorInfo());
  */


  /*Add a subscriber to playlist, for teachers and users
  * to follow teachers playlists
  * @param playlist id and user od
  * @return 
  */
  public function addSubscriber($playlist_id, $user_id) {
      $sql = "INSERT INTO playlist_subscriptions (playlist_id, user_id) VALUES (?, ?)";
      //$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
      $sth = $this->db->prepare($sql);
      $sth->execute(array($playlist_id, $user_id));
      //print_r($sth->errorInfo());
  }
  /*Get all playlists in databases
  * @param 
  * @return array of data on all playlists
  */
  public function getAllPlaylists() {
        $sql = 'SELECT * FROM playlist';
        $sth = $this->db->prepare($sql);
        $sth-> execute ();
        if ($row = $sth->fetchAll()) {
            return $row;
        }
    }
  /*Get all videos that a given playlists contains
  * @param playlist id
  * @return row of videodata on each video
  */
  public function getPlaylistVideos($playlistid) {
      // Missing check for same video existing in same video list TODO
     $sql = "SELECT video_id, title, description, emne FROM video WHERE video_id IN (SELECT fk_video_id FROM playlist_video WHERE fk_playlist_id=? ORDER BY video_order ASC)";
      $sth = $this->db->prepare($sql);
      $sth->execute(array($playlistid));
      if ($row = $sth->fetchAll()) {
          return $row;
      }
    }
  /*Update the data of the playlist
  * @param array of data new data and playlist id
  * @return status array
  */
  public function editPlaylist($playlistData, $playlist_id) {
      $sql = "UPDATE playlist SET playlist_title = ?, description = ? WHERE playlist_id=?";
      $sth = $this->db->prepare($sql);
      $sth-> execute (array($playlistData['title'], $playlistData['descr'], $playlist_id));
      if ($sth->rowCount()==1) {
            $tmp['status'] = 'OK';
          } else {
            $tmp['status'] = 'FAIL';
          }
      return $tmp;
  }
  /*Edit videos in playlist, add/delete/change order
  * @param array of videos. playlist id
  * @return
  */

  public function editPlaylistVideos($videos, $playlist_id) {
        // Firstly deletes all of playlists videos
        $sql = "DELETE FROM playlist_video WHERE fk_playlist_id=?";
        $sth = $this->db->prepare($sql);
        $sth-> execute(array($playlist_id));
        // Then adds new videos
        // a bit lazy and uneffective, but best we managed on the time left
        if ($sth->rowCount()>=1) {
          $sql = "insert into playlist_video (fk_video_id, fk_playlist_id, video_order) VALUES (?,?,?)";
          $sth = $this->db->prepare($sql);
          foreach ($videos as $key => $videoid) {
              echo $videoid;
              $sth->execute(array($videoid, $playlist_id, $key));
              }
          } 
  }
  /*Delete given playlist
  * @param playlist id
  * @return
  */
  public function deletePlaylist($playlist_id) {
      $sql = "DELETE FROM playlist WHERE playlist_id=?";
      $sth = $this->db->prepare($sql);
      $sth-> execute (array($playlist_id));
      if ($sth->rowCount()==1) {
            $tmp['status'] = 'OK';
          } else {
            $tmp['status'] = 'FAIL';
          }
      return $tmp;
  }
  /*Find playlists that has videos that matches keyword search
  *used in browse page
  * @param keyword
  * @return row of matched playlists
  */
   public function getPlaylistMatching($keyword) {
    // Get playlist that has videos containing the keyword, not the most effective but oh well
    $sql = "SELECT
                *
            FROM
                playlist
            WHERE
                playlist_id IN(
                SELECT
                    fk_playlist_id
                FROM
                    playlist_video
                WHERE
                    fk_video_id IN(
                    SELECT
                        video_id
                    FROM
                        video
                    WHERE
                        title LIKE ? OR description LIKE ? OR emne LIKE ? OR fk_owner_id LIKE(
                        SELECT
                            id
                        FROM
                            USER
                        WHERE
                            firstname LIKE ?
                    )
                )
            )";
    $sth = $this->db->prepare($sql);
    $sth->execute(array("%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"));
    if ($row = $sth->fetchAll()) {
            return $row;
        }
   }
  /*Make new playlist
  * @param playlist title, description and owner
  * @return
  */
  public function createPlaylist($title, $description, $user) {
      $sql = "insert into playlist (playlist_title, description ,created_by_user) VALUES (?, ?, ?)";
      $sth = $this->db->prepare($sql);
      $sth->execute(array($title, $description, $user ));
  }
  /*Add video to playlist, called after createPlaylist to add videos
  * @param video id to add, playlist id and key that gives order of videos
  * @return
  */
  public function addVideoToPlaylist($videoid,$playlistid,$key)
  {
      // Missing check for same video existing in same video list TODO

      $sql = "insert into playlist_video (fk_video_id, fk_playlist_id, video_order) VALUES (?,?,?)";
      $sth = $this->db->prepare($sql);
      $sth->execute(array($videoid, $playlistid, $key));

  }
  /*Get playlist data based on id
  * @param playlist id
  * @return row of data for given playlist
  */
  public function getPlaylist($playlist_id) {
      $sql = "SELECT * from playlist WHERE playlist_id=?";
      $sth = $this->db->prepare($sql);
      $sth->execute(array($playlist_id));
       if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
          return $row;
      }
  }
  /*Get all playlists for one teaecher
  * @param teacher id
  * @return array of playlist that matches WHERE clause
  */
  public function getTeacherPlaylists($teacher_id) {
      $sql = "SELECT * from playlist WHERE created_by_user=?";
      $sth = $this->db->prepare($sql);
      $sth->execute(array($teacher_id));
      if ($row = $sth->fetchAll()) {
          return $row;
      }
  }



  }


?>