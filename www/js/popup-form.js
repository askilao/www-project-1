
/*Pop up forms for uploading file, editing, creating and editing playlists
*/
//when create playlist button is pressed show form

function openPlaylistForm() {
  document.getElementById("playlistForm").style.display = "block";
}
//when close button is pressed close form
function closePlaylistForm() {
  document.getElementById("playlistForm").style.display = "none";
}

function openUploadForm() {
  document.getElementById("uploadForm").style.display = "block";
}

function closeUploadForm() {
  document.getElementById("uploadForm").style.display = "none";
}

// when editing the current data is put as text in inputs
function openEditForm(title, descr, subject, video_id) {
	document.getElementsByName('newTitle')[0].value=title;
	document.getElementsByName('newDescr')[0].value=descr;
	document.getElementsByName('newSubject')[0].value=subject;
	document.getElementsByName('editVideo_id')[0].value=video_id;
  	document.getElementById("editForm").style.display = "block";
}

function closeEditForm() {
  document.getElementById("editForm").style.display = "none";
}

function openPlaylistEditForm(title, descr, playlists, playlist_id) {
  document.getElementsByName('newPlaylistTitle')[0].value=title;
  document.getElementsByName('newPlaylistDescr')[0].value=descr;
  document.getElementsByName('editPlaylist_id')[0].value=playlist_id;
  document.getElementById("playlistEditForm").style.display = "block";
}

function closePlaylistEditForm() {
  document.getElementById("playlistEditForm").style.display = "none";
}