$(document).ready(function() {
    $("#submitLogin").click(function () {
        checkLoginInput();
    }); 
    $("#showPw").change(function(){
        if($(this).is(':checked')){
         $("#password").attr("type","text");
         $("#showPwText").text("Hide");
        }else{
         // Changing type attribute
         $("#password").attr("type","password");
        
         // Change the Text
         $("#showPwText").text("Show");
        }  
    });
})

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
        url: "../../../backend/requestHandler.php",
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
                $("#post-response").append("The Login was successful.<br>");
                $('#login-form')[0].reset();
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

function checkLoginStatus() {
    $.ajax({
        type: "POST",
        url: "../../../backend/requestHandler.php",
        data: jQuery.param({
            method: "getLoginStatus", 
        }),
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
            if(response["isLoggedIn"] === true){
                $("#post-response").append("You are now logged in with username " + response["username"] + ".");
                $('#login-form')[0].reset();
            }
            else if(response["isLoggedIn"] === false){
                $("#post-response").append("Your Login-Data was not saved in this session!<br> Some functions may be unavailable. For the best user experience, please try again to log in.");
            }

        },
        error: function(error){
            $("#post-response").text("Oops, something went wrong. Please try again!");

        }
    });
}


function emptyLoginErrors(){
    $("#user_error").empty();
    $("#password_error").empty();
    $("#confirm_password_error").empty();
    $("#post-response").empty();
}