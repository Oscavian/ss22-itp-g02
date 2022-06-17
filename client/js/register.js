function signupShowHideNewPw(){
    if($("#signupNewPassword").attr("type") === "password"){
        $("#signupNewPassword").attr("type","text");
        $("#signupNewPasswordConfirm").attr("type","text");
        $("#signupNewPasswordShow").attr("class", "bi bi-eye-slash-fill");
        $("#signupNewPasswordConfirmShow").attr("class", "bi bi-eye-slash-fill");
        return;
    }
    
    $("#signupNewPassword").attr("type","password");
    $("#signupNewPasswordConfirm").attr("type","password");
    $("#signupNewPasswordShow").attr("class", "bi bi-eye-fill");
    $("#signupNewPasswordConfirmShow").attr("class", "bi bi-eye-fill");
}

function checkRegisterInput() {

    emptyErrors();
    var allOk = true;
    allOk = checkIfEmpty(allOk);
    allOk = checkIfAlphabet(allOk);
    allOk = checkIfAlphanumeric(allOk);
    allOk = checkLength(allOk);
    allOk = checkPw(allOk);

    if(allOk === true) {
        checkUserNameAvailable();
    }
}

function checkIfEmpty(allIsOk) {
    var allOk = allIsOk;
    if($("#singupFirstName").val() === ""){
        allOk = false;
        $("#singupFirstNameError").text("Bitte geben Sie einen Vornamen ein");
    }
    if ($("#singupLastName").val() === ""){
        allOk = false;
        $("#singupLastNameError").text("Bitte geben Sie einen Nachnamen ein");
    }
    if($("#signupUsername").val() === ""){
        allOk = false;
        $("#signupUsernameError").text("Bitte geben Sie einen Benutzernamen ein");
    }
    if($("#signupNewPassword").val() === ""){
        allOk = false;
        $("#signupNewPasswordError").text("Bitte geben Sie ein Passwort ein");
    }
    if($("#signupNewPasswordConfirm").val() === ""){
        allOk = false;
        $("#signupNewPasswordConfirmError").text("Bitte bestätigen Sie das Passwort");
    }
    return allOk;
}

function checkIfAlphabet(allIsOk){
    //checks if first and last name consist of letters only
    var allOk = allIsOk;
    var firstName = $("#singupFirstName").val();
    var lastName = $("#singupLastName").val();
    if(!/^[A-Za-zäöüÄÖÜß\s]*$/.test(firstName)){
        allOk = false;
        $("#singupFirstNameError").text("Der Vorname darf nur Buchstaben enthalten");
    }
    if(!/^[A-Za-zäöüÄÖÜß\s]*$/.test(lastName)){
        allOk = false;
        $("#singupLastNameError").text("Der Nachname darf nur Buchstaben enthalten");
    }
    return allOk;
}

function checkIfAlphanumeric(allIsOk){
    var allOk = allIsOk;
    var user = $("#signupUsername").val();
    var password = $("#signupNewPassword").val();
    if(!/^[a-zA-Z0-9äöüÄÖÜß\.]+$/.test(user)){
        allOk = false;
        $("#signupUsernameError").text("Der Benutzername darf keine Sonderzeichen enthalten");
    }
    if(/[\s\\]+$/.test(password)){
        allOk = false;
        $("#signupNewPasswordError").text("Das Passwort darf kein '\\' oder Leerzeichen enthalten");
    }
    return allOk;
}

function checkLength(allIsOk){
    var allOk = allIsOk;
    if($("#singupFirstName").val().length > 50){
        allOk = false;
        $("#singupFirstNameError").text("Der Vorname darf nicht mehr als 50 Zeichen haben");
    }
    if($("#singupLastName").val().length > 50){
        allOk = false;
        $("#singupLastNameError").text("Der Nachname darf nicht mehr als 50 Zeichen haben");
    }
    if($("#signupUsername").val().length < 6 || $("#signupUsername").val().length > 50){
        allOk = false;
        $("#signupUsernameError").text("Der Benutzername muss zwischen 6 und 50 Zeichen lang sein");
    }
    if($("#signupNewPassword").val().length < 6 || $("#signupNewPassword").val().length > 50){
        allOk = false;
        $("#signupNewPasswordError").text("Das Passwort muss zwischen 6 und 50 Zeichen lang sein");
    }
    return allOk;
}

function checkPw(allIsOk){
    //checks if password and confirmation are the same
    var allOk = allIsOk;
    var pw = $("#signupNewPassword").val();
    var cpw = $("#signupNewPasswordConfirm").val();
    if(pw !== cpw){
        $("#signupNewPasswordConfirmError").text("Die Passwörter stimmen nicht überein");
        allOk = false;
    }
    return allOk;
}

function checkUserNameAvailable(){

    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "checkUserNameAvailable", user: $("#signupUsername").val()},
        cache: false,
        dataType: "json",
        success: function (response) {
            if (response["userNameAvailable"]) {
                submitRegisterInput();
                return;
            }
            $("#signupUsernameError").text("Dieser Benutzername ist bereits vergeben");
            
        },
        error: function (error) {
            console.log(error);
            alert("Error checking username!");
        }
    });
}

function submitRegisterInput() {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data:   {method: "registerTeacher",
                first_name: $("#singupFirstName").val(),
                last_name: $("#singupLastName").val(),
                user: $("#signupUsername").val(),
                password: $("#signupNewPassword").val(),
                },
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
            $("#signupModal").modal("hide");
            loadDefaultPage();
            checkLoginStatus();
        },
        error: function(error){
            console.log(error);
        }
    });
}

function emptyErrors(){
    $("#singupFirstNameError").empty();
    $("#singupLastNameError").empty();
    $("#signupUsernameError").empty();
    $("#signupNewPasswordError").empty();
    $("#signupNewPasswordConfirmError").empty();
}