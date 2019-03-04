/* Rating of video is radio box with css stars
*/

$(document).ready(function(){
    // Check Radio-box
    $(".rating input:radio").attr("checked", false);

    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });
    //send given rating as post request
    $('input:radio').change(
      function(){
        var userRating = this.value;
        var video_id = document.getElementById("video_id").value;
        var url = 'giveRating.php';
        //data to be seny
        var formData = new FormData();
        formData.append('rating', userRating);
        formData.append('video_id', video_id);
        //send data and keep session data
        fetch(url, { method: 'POST', body: formData, credentials:"same-origin" })
        .then(function (response) {
          location.reload();
          return response.text();

        })
        .then(function (body) {
          console.log(body);
        });    
        
    });
      
});