$(document).ready(function() {
    $("#logout").click(function () {
        logoutUser();
    }); 
    
});

function logoutUser() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: jQuery.param({
            method: "logout",  
        }),
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
                alert("You were logged out successfully");
                checkLoginStatus();
        },
        error: function(error){
            alert("An error happened while trying to log you out.");

        }
    });
}
