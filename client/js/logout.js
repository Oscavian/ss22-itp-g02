$(document).ready(function() {
    $("#logout").click(function () {
        logoutUser();
    }); 
    
});

function logoutUser() {
    $.ajax({
        type: "POST",
        url: "../../../backend/requestHandler.php",
        data: jQuery.param({
            method: "logout",  
        }),
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
            if(response["success"] === true){
                $("#post-response").append("You were logged out successfully<br>");
            }
            else{
                $("#post-response").text("The Logout was unsuccessful, please try again later.");
            }

        },
        error: function(error){//wtf ist error eigentlich
            $("#post-response").text("An error happened while trying to log you out.");

        }
    });
}
