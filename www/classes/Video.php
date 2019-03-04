<?php
// Scale thumbnail
include 'scale.php';

class Video {
private $db, $id;
private $videoData = [];


public function __construct($db){
  $this->db = $db;
}

/*Get all videos that a given teacher has uploaded
* @param teacher_id
* @return row video data
*/
public function getVideos($id) {
$sql = 'SELECT video_id, title, description, emne FROM video WHERE fk_owner_id=?';
$sth = $this->db->prepare($sql);
$sth-> execute (array ($id));
  if ($row = $sth->fetchAll()) {
     return $row;
  }
}

/*Get data on owner of video
* @param teacher_id
* @return array of data of given teacher
*/

public function getOwnerById($teacher_id){
$sql = 'SELECT id, firstname FROM user WHERE id=?';
$sth = $this->db->prepare($sql);
$sth-> execute (array ($teacher_id));
  if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
  return $row;
}
}
 /*Insert comment into database
  * @param array of comment data (video id, mail of commenter, time commented, comment text)
  * @return status array
  */
public function postComment($commentData) {
$sql = "INSERT INTO comment (video_id, mail, time, comment) VALUES (?, ?, ?, ?)";
//$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$sth = $this->db->prepare($sql);
$sth->execute(array($commentData['video_id'], $commentData['mail'], $commentData['time'], $commentData['comment']));
//print_r($sth->errorInfo());
if ($sth->rowCount()==1) {
  $tmp['status'] = 'OK';
} else {
  $tmp['status'] = 'FAIL';
}
return $tmp;
}

 /*Get videodata based on its id
  * @param video id
  * @return array of video data
  */
public function getVideo($id) {
    $sql = 'SELECT fk_owner_id, video_id, title, filename ,description, emne FROM video WHERE video_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($id));
      if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      return $row;
    }

}
 /*Get all comments on a video
  * @param video id
  * @return data of all the comments
  */
public function getComments($id) {
    $sql = 'SELECT comment, mail, time FROM comment WHERE video_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array ($id));
      if ($row = $sth->fetchAll()) {
         return $row;
      }

}

 /*Video search based on keyword
  * @param keyword
  * @return array of all videodata that matched keyword
  */
public function getVideosMatching($keyword) {
    $sql = "SELECT video_id, fk_owner_id, title, filename, description, emne FROM video WHERE title LIKE ? OR description LIKE ? OR emne LIKE ? OR fk_owner_id LIKE (SELECT id FROM user WHERE firstname LIKE ?)";
    $sth = $this->db->prepare($sql);
    $sth-> execute(array("%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"));
    if ($row = $sth->fetchAll()) {
        return $row;
    }

}
 /*Get all videos in database
  * @param 
  * @return array of all videos
  */
public function getAllVideos() {
    $sql = 'SELECT video_id, title, description, thumbnail, emne FROM video';
    $sth = $this->db->prepare($sql);
    $sth-> execute ();
    if ($row = $sth->fetchAll()) {
        return $row;
    }

}
 /*Update data of a given video
  * @param video id and data update
  * @return status array
  */

public function editVideo($video_id, $videoData) {
    $sql = "UPDATE video SET title = ?, description = ?, emne = ? WHERE video_id=?";
    //$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $sth = $this->db->prepare($sql);
    $sth-> execute (array($videoData['title'], $videoData['descr'], $videoData['subject'], $video_id));
    //print_r($sth->errorInfo());
    if ($sth->rowCount()==1) {
          $tmp['status'] = 'OK';
        } else {
          $tmp['status'] = 'FAIL';
        }
    return $tmp;
}
 /*Delete video based on id
  * @param video id
  * @return status array
  */


public function deleteVideo($video_id,$id=0) {
    // Deletes from disk
    $this->deleteFromDisk($video_id);
    // Remove From Playlist
    $sql = "DELETE FROM playlist_video WHERE fk_video_id=?";
    $sth = $this->db->prepare($sql);
    $sth-> execute (array($video_id));

    // Removes video from playlist
    if($id != 0) {
        $sql = "delete from playlist_subscriptions where user_id = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($id));

        $sql = "delete from playlist where user_id = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($id));

    }

    // Deletes Video in DB
    $sql = "DELETE FROM video WHERE video_id=?";
    //$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $sth = $this->db->prepare($sql);
    $sth-> execute (array($video_id));

    return $tmp['status'] = 'OK';

    /*if ($this->deleteFromDisk($video_id)) {
        // Check if video is in some playlist then remove from playlists
        $sql = "DELETE FROM playlist_video where  fk_video_id = ?";
        $sth = $this->db->prepare($sql);
        $sth-> execute (array($video_id));

    $sql = "DELETE FROM video WHERE video_id=?";
    //$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $sth = $this->db->prepare($sql);
    $sth-> execute (array($video_id));
    //print_r($sth->errorInfo());
    if ($sth->rowCount()==1) {
          $tmp['status'] = 'OK';
        } else {
          $tmp['errorMessage'] = "Cant delete video, maybe it is used in a playlist ?";
          $tmp['status'] = 'FAIL';
        }
    } else {
        $tmp['status'] = 'FAIL'; 
    }*/
    return $tmp['status'] = 'OK';
}

 /*Insert new video data into database 
  * @param video data
  * @return status array
  */

public function newVideo($videoData) {
$sql = "INSERT INTO video (fk_owner_id, title, filename, extension, mime, size, description, emne) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$sth = $this->db->prepare($sql);
$sth->execute(array($videoData['owner'], $videoData['title'], $videoData['filename'], $videoData['extension'], $videoData['mime'], $videoData['size'], $videoData['description'], $videoData['emne']));

//If data was added to database put  file on disk
$tmp['filename'] = $videoData['filename'];
if ($sth->rowCount()==1) {
    $this->videoData = $videoData;
    $this->id = $this->db->lastInsertId();
    if($this->moveToDisk()) {
      $tmp['status'] = 'OK';
    } else {
      $tmp['status'] = 'FAIL';
      $tmp['errorMessage'] = 'Could not move to file';
    }
  } else {
    $tmp['status'] = 'FAIL';
    $tmp['errorMessage'] = 'Could not create user';
  }
  // Error in SQL
  if ($this->db->errorInfo()[1]!=0) { 
    $tmp['errorMessage'] = $this->db->errorInfo()[2];
  }
return $tmp; 

}
 /*If videodata was added to database, the video file is put on disk
  * @param 
  * @return status variable
  */
private function moveToDisk() {
    $path = 'uploadedFiles/';
    $success = -1;
    //Checks if a filename is set, the extension is correct and the file data has been updated and added to the database
    //If a user hasn't uploaded any files before, a new folder will be created with the user id as folder name,
    //and videos saved within this folder titled with the video_id:

    //if filename is set
    if (isset($this->videoData['filename'])) {
        // if file extension is approved
        if (( $this->videoData['extension'] == ".mp4") || ( $this->videoData['extension'] == ".ogg") || ( $this->videoData['extension'] == ".webm")) {
                if (@move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "uploadedFiles/$this->id"."_".$this->videoData['filename'])) {
                    //all videos have same name structure (videoid_filename)
                    $video = "uploadedFiles/$this->id" . "_" . $this->videoData['filename'];
                    // if successfully uploaded then create thumbnail
                    $this->createThumbnail($video,$this->id);
                    $success = 1;
                } else {
                    $success = 0;
                  }     
            } 
    }
return $success;
}

 /*If data is deleted from table it should be deleted from disk
  * @param video id of video to be removed
  * @return success boolean
  */
public function deleteFromDisk($video_id) {
    $path = 'uploadedFiles/';
    $sql = 'SELECT filename FROM video WHERE video_id=?';
    $sth = $this->db->prepare($sql);
    $sth-> execute (array($video_id));
    if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        //set videopath (uploadedFiles/videoid_filename)
        $videopath = $path . "/$video_id" . "_" . $row['filename'];
        //if the file is on disk then delete it
        if (file_exists($videopath)) {
            //unlink is delete in php
            unlink($videopath);
            return true;
        } else {
            return false;
        }
    }
    else {
        return false;
    }
}
 /*Create thumbnail of uploaded video
 * video must first be uploaded then a thumbnail can be made
  * @param video id and video data
  * @return
  */

private function createThumbnail($video,$id) {
    //default thumbnail size
    $width = 150;
    $height = 150;
    //set filepath for temp thumbnail image
    $thumbnail = $video . '_thumbnail.png';
    //uses ffmpeg linux command to get a frame of the video 5 seconds in
    // puts image in selected path
    shell_exec("ffmpeg -i $video -ss 5 -t 00:00:01 -vframes 1 $thumbnail 2>&1");
    //get created thumbnail from disk
    $content = file_get_contents($thumbnail);
    //scale and get proper dataform of image
    $db_thumbnail = scale(imagecreatefromstring($content),$width,$height);
    //insert thumbnail into database and delete from disk
    $sql = "UPDATE video SET thumbnail=? WHERE video_id=?";
    $sth = $this->db->prepare($sql);
    $sth->execute(array($db_thumbnail, $id));
    unlink($thumbnail);

}
 /*Get rating of a given video
  * @param video id 
  * @return rating int
  */
public function getRating($id) {

    $sql = "SELECT rating from video_viewcount inner join video on fk_video_id = video.video_id where fk_video_id = ?";
    $sth = $this->db->prepare($sql);
    $sth->execute(array($id));
     if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        return $row['rating'];
    }
}
 /*Set rating on a video
  * @param video id and rating int
  * @return 
  */
public function giveRating($id, $rating) {
//First time rated
if (!$this->getRating($id)) {
    $sql = "INSERT INTO video_viewcount (fk_video_id, votes, sum_of_vote) VALUES (?, ?, ?)";
    $sth = $this->db->prepare($sql);
    $sth->execute(array($id, 1, $rating));
// not first time
// update votes by one and add sum of votes
// database calculates vote (sum_of_votes / total votes)
} else {
    $sql = "UPDATE video_viewcount SET votes = votes + 1, sum_of_vote = sum_of_vote + ? WHERE fk_video_id=?";
    $sth = $this->db->prepare($sql);
    $sth->execute(array($rating, $id));

    }
}


 /*Get title of video based on id
  * @param video id
  * @return video title string
  */
public function getVideoTitle($video_id) {
    $sql = "SELECT title from video WHERE video_id=?";
    $sth = $this->db->prepare($sql);
    $sth->execute(array($video_id));
     if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        return $row['title'];
    }
}

 /*Get video with matching a subject
 *Used when showing video to give a "related videos" section 
  * @param video id
  * @return array of videos
  */
public function getVideosBySubject($id) {
    $sql = 'SELECT
                fk_owner_id,
                video_id,
                title,
                filename,
                description,
                emne
            FROM
                video
            WHERE
                emne IN(
                SELECT
                    emne
                FROM
                    video
                WHERE
                    video_id = ?
            )';
    $sth = $this->db->prepare($sql);
    $sth->execute(array($id));
    if ($row = $sth->fetchAll()) {
        return $row;
    }

}

}



?>