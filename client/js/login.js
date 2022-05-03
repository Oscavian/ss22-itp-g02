function checkLoginInput() {
    emptyLoginErrors();
    var allOk = true;
    allOk = checkIfEmptyLogin(allOk);
    allOk = checkIfAlphanumeric(allOk);
    allOk = checkLengthLogin(allOk);

    if(allOk === true) {
        submitLoginInput();
    }
}
function checkIfEmptyLogin(allIsOk) {
    var allOk = allIsOk;
    if($("#user").val() === ""){
        allOk = false;
        $("#user_error").append("Please enter a username <br>");
    }
    if($("#password").val() === ""){
        allOk = false;
        $("#password_error").append("Please enter a password <br>");
    }
    return allOk;
}

function checkLengthLogin(allIsOk){
    var allOk = allIsOk;
    if($("#user").val().length < 6 || $("#user").val().length > 50){
        allOk = false;
        $("#user_error").append("Username must be between 6 to 50 characters long <br>");
    }
    if($("#password").val().length < 6 || $("#password").val().length > 50){
        allOk = false;
        $("#password_error").append("Password must be between 6 to 50 characters long <br>");
    }
    return allOk;
}

function submitLoginInput() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: jQuery.param({
            method: "login",
            user: $("#user").val(),
            password: $("#password").val(),   
        }),
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
            if(response["success"] === true){
                $("#post-response").text("The Login was successful.");
                $('#login-form')[0].reset();
                setTimeout(function(){
                    $("#loginModal").modal("hide");
                }, 2000);
                checkLoginStatus();
            }
            else if(response["success"] === false){
                $("#post-response").text("The Login was unsuccessful, please try again later.");
            }

        },
        error: function(error){//wtf ist error eigentlich
            $("#post-response").text("An error happened while trying to log you in.");

        }
    });
}



function emptyLoginErrors(){
    $("#user_error").empty();
    $("#password_error").empty();
    $("#confirm_password_error").empty();
    $("#post-response").empty();
}