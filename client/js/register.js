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
        $("#singupFirstNameError").text("Please enter a first name <br>");
    }
    if ($("#singupLastName").val() === ""){
        allOk = false;
        $("#singupLastNameError").text("Please enter a last name <br>");
    }
    if($("#signupUsername").val() === ""){
        allOk = false;
        $("#signupUsernameError").text("Please enter a username <br>");
    }
    if($("#signupNewPassword").val() === ""){
        allOk = false;
        $("#signupNewPasswordError").text("Please enter a password <br>");
    }
    if($("#signupNewPasswordConfirm").val() === ""){
        allOk = false;
        $("#signupNewPasswordConfirmError").text("Please confirm your password <br>");
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
        $("#singupFirstNameError").text("First name must consist of letters only <br>");
    }
    if(!/^[A-Za-zäöüÄÖÜß\s]*$/.test(lastName)){
        allOk = false;
        $("#singupLastNameError").text("Last name must consist of letters only <br>");
    }
    return allOk;
}

function checkIfAlphanumeric(allIsOk){
    var allOk = allIsOk;
    var user = $("#signupUsername").val();
    var password = $("#signupNewPassword").val();
    if(!/^[a-zA-Z0-9äöüÄÖÜß\.]+$/.test(user)){
        allOk = false;
        $("#signupUsernameError").text("Username must consist of letters and numbers only <br>");
    }
    if(/[\s\\]+$/.test(password)){
        allOk = false;
        $("#signupNewPasswordError").text("Das Passwort darf kein '\\' oder Leerzeichen enthalten!");
    }
    return allOk;
}

function checkLength(allIsOk){
    var allOk = allIsOk;
    if($("#singupFirstName").val().length > 50){
        allOk = false;
        $("#singupFirstNameError").text("Only a maximum of 50 characters allowed <br>");
    }
    if($("#singupLastName").val().length > 50){
        allOk = false;
        $("#singupLastNameError").text("Only a maximum of 50 characters allowed <br>");
    }
    if($("#signupUsername").val().length < 6 || $("#signupUsername").val().length > 50){
        allOk = false;
        $("#signupUsernameError").text("Username must be between 6 to 50 characters long <br>");
    }
    if($("#signupNewPassword").val().length < 6 || $("#signupNewPassword").val().length > 50){
        allOk = false;
        $("#signupNewPasswordError").text("Password must be between 6 to 50 characters long <br>");
    }
    return allOk;
}

function checkPw(allIsOk){
    //checks if password and confirmation are the same
    var allOk = allIsOk;
    var pw = $("#signupNewPassword").val();
    var cpw = $("#signupNewPasswordConfirm").val();
    if(pw !== cpw){
        $("#signupNewPasswordConfirmError").text("Password and its confirmation must match <br>");
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
            $("#signupUsernameError").text("Dieser Username ist bereits vergeben!");
            
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