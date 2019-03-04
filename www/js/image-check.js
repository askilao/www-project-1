/* Give css to image when checked
*/
$(document).ready(function(e){
    		$(".img-responsive").click(function(){
				$(this).toggleClass("check");

			});
});

var order = [];

/* Get order of playlists as they are checked
*/

$("[type=checkbox]").on('change', function() { // always use change event
    var idx = order.indexOf(this.value);
	
    if (!this.checked) {         // if already in array
    	order.splice(idx, 1); // make sure we remove it
        // set index in playlist as overlay on thumbail
    	document.getElementById("index:" + this.value).innerHTML = " ";
        document.getElementById("playlistindex:" + this.value).innerHTML = " ";
    }

    if (this.checked) {    // if checked
    	order.push(this.value);  // add to end of array
    	document.getElementById("index:" + this.value).innerHTML = order.indexOf(this.value) + 1;
        document.getElementById("playlistindex:" + this.value).innerHTML = order.indexOf(this.value) + 1;

    }
   
});

/*submit created playlist
*/
function submitPlaylist() {
    // get element values
    var playlistName = document.getElementById("playlist_name").value;
    var playlistDescription = document.getElementById("playlist_description").value;
    // page to be posted to
    var url = 'playlist.php';
    //create formdata to sent with fetch
    var formData = new FormData();
    formData.append('playlist_name', playlistName);
    formData.append('playlist_description', playlistDescription);
    //add checked playlists
    for (var i = 0; i < order.length; i++) {
	    formData.append('checkbox[]', order[i]);
	}
    // use fetch API to post form to php
    fetch(url, { method: 'POST', body: formData, credentials:"same-origin" })
    .then(function (response) {
      location.reload();
      return response.text();

    })
    .then(function (body) {
      console.log(body);
    });  
}
// edit playlist, same as submit but with other values
function editPlaylist() {
    var playlistName = document.getElementById("newPlaylistTitle").value;
    var playlistDescription = document.getElementById("newPlaylistDescr").value;
    var playlistId = document.getElementById("editPlaylist_id").value;
    var url = 'playlist.php';
    var formData = new FormData();
    formData.append('editPlaylist_name', playlistName);
    formData.append('editPlaylist_description', playlistDescription);
    formData.append('editPlaylist_id', playlistId);
    for (var i = 0; i < order.length; i++) {
        formData.append('checkbox[]', order[i]);
    }
    fetch(url, { method: 'POST', body: formData, credentials:"same-origin" })
    .then(function (response) {
      location.reload();
      return response.text();

    })
    .then(function (body) {
      console.log(body);
    });  
}

