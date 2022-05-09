function loginShowHidePw(){
    if($("#login-modal-showPw").is(':checked')){
        $("#login-modal-password").attr("type","text");
        $("#login-modal-showPwText").text("Hide");
        return;
    }
    // Changing type attribute
    $("#login-modal-password").attr("type","password");

    // Change the Text
    $("#login-modal-showPwText").text("Show");
}

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

function checkIfAlphanumeric(allIsOk){
    var allOk = allIsOk;
    var user = $("#login-modal-user").val();
    var password = $("#login-modal-password").val();
    if(!/^[a-zA-Z0-9]+$/.test(user)){
        allOk = false;
        $("#login-modal-user-error").append("Username must consist of letters and numbers only <br>");
    }
    if(!/^[a-zA-Z0-9]+$/.test(password)){
        allOk = false;
        $("#login-modal-password-error").append("Password must consist of letters and numbers only <br>");
    }
    return allOk;
}

function checkIfEmptyLogin(allIsOk) {
    var allOk = allIsOk;
    if($("#login-modal-user").val() === ""){
        allOk = false;
        $("#login-modal-user-error").append("Please enter a username <br>");
    }
    if($("#login-modal-password").val() === ""){
        allOk = false;
        $("#login-modal-password-error").append("Please enter a password <br>");
    }
    return allOk;
}

function checkLengthLogin(allIsOk){
    var allOk = allIsOk;
    if($("#login-modal-user").val().length < 6 || $("#login-modal-user").val().length > 50){
        allOk = false;
        $("#login-modal-user-error").append("Username must be between 6 to 50 characters long <br>");
    }
    if($("#login-modal-password").val().length < 6 || $("#login-modal-password").val().length > 50){
        allOk = false;
        $("#login-modal-password-error").append("Password must be between 6 to 50 characters long <br>");
    }
    return allOk;
}

function submitLoginInput() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "login", user: $("#login-modal-user").val(), password: $("#login-modal-password").val()},
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["success"]){
                $('#login-form')[0].reset();
                $("#loginModal").modal("hide");
                checkLoginStatus();
                return;
            }

            $("#login-modal-post-response").text("The User or password was not correct.");
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });
}

function emptyLoginErrors(){
    $("#login-modal-user-error").empty();
    $("#login-modal-password-error").empty();
    $("#login-modal-post-response").empty();
}