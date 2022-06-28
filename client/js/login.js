function loginShowHidePw(){
    if($("#login-modal-password").attr("type") === "password"){
        $("#login-modal-password").attr("type","text");
        $("#login-modal-password-show").attr("class", "bi bi-eye-slash-fill");
        return;
    }
    
    $("#login-modal-password").attr("type","password");
    $("#login-modal-password-show").attr("class", "bi bi-eye-fill");
}

function checkLoginInput() {
    emptyLoginErrors();
    var allOk = true;
    allOk = checkIfEmptyLogin(allOk);
    allOk = checkIfAlphanumericLogin(allOk);
    allOk = checkLengthLogin(allOk);

    if(allOk === true) {
        submitLoginInput();
    }
}

function checkIfAlphanumericLogin(allIsOk){
    var allOk = allIsOk;
    var user = $("#login-modal-user").val();
    var password = $("#login-modal-password").val();
    if(!/^[a-zA-Z0-9äöüÄÖÜß\.]+$/.test(user)){
        allOk = false;
        $("#login-modal-user-error").text("Der Benutzername darf keine Sonderzeichen enthalten");
    }
    return allOk;
}

function checkIfEmptyLogin(allIsOk) {
    var allOk = allIsOk;
    if($("#login-modal-user").val() === ""){
        allOk = false;
        $("#login-modal-user-error").text("Bitte geben Sie einen Benutzernamen ein");
    }
    if($("#login-modal-password").val() === ""){
        allOk = false;
        $("#login-modal-password-error").text("Bitte geben Sie ein Passwort ein");
    }
    return allOk;
}

function checkLengthLogin(allIsOk){
    var allOk = allIsOk;
    if($("#login-modal-user").val().length < 6 || $("#login-modal-user").val().length > 50){
        allOk = false;
        $("#login-modal-user-error").text("Der Benutzername muss zwischen 6 und 50 Zeichen lang sein");
    }
    if($("#login-modal-password").val().length < 6 || $("#login-modal-password").val().length > 50){
        allOk = false;
        $("#login-modal-password-error").text("Das Passwort muss zwischen 6 und 50 Zeichen lang sein");
    }
    return allOk;
}

function submitLoginInput() {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "login", user: $("#login-modal-user").val(), password: $("#login-modal-password").val()},
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["success"]){
                $('#login-form')[0].reset();
                emptyLoginErrors();
                $("#loginModal").modal("hide");
                notyf.success('Login erfolgreich!<br>Willkommen!');
                checkLoginStatus();
                loadDefaultPage();
                return;
            }

            $("#login-failed-error").show();
        },
        error: function(error){
            console.log(error);
        }
    });
}

function emptyLoginErrors(){
    $("#login-modal-user-error").empty();
    $("#login-modal-password-error").empty();
    $("#login-modal-post-response").empty();
}

//------Enables enter key to submit login-------

$('#loginModal').on('shown.bs.modal', function () {
    document.addEventListener("keydown", enterKey);
})

$('#loginModal').on('hide.bs.modal', function () {
    document.removeEventListener("keydown", enterKey);
    emptyLoginErrors();
})

function enterKey(event){
    if (event.key === "Enter") {
        $("#login-modal-submit-button").click();
    }
}