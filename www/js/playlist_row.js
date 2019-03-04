
//Make playlist row clickable
// sends user to showVideo page with playlist id
jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});
