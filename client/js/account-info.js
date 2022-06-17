loadUserInfo();

$("#account-info-avatar").attr("src", rootPath + "/client/assets/img/blank-profile-picture.png");

function loadUserInfo() {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "getLoginStatus"},
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["isLoggedIn"]){
                let userType = response["userType"];
                if (response["userType"] === 1){userType = "Lehrer*in";}
                if (response["userType"] === 2){userType = "Schüler*in";}
                $("#userTypeField").text(userType);
                $("#userFullNameField").text(response["firstName"] + " " + response["lastName"]);
                $("#firstNameField").text(response["firstName"]);
                $("#lastNameField").text(response["lastName"]);
                $("#usernameField").text(response["username"]);
                return;
            }

            alert("You are not logged in!");
        },
        error: function (error) {
            console.log(error);
            alert("Error checking login status!");
        }
    });
}


//--------------Change User-Data Functions---------------//

$("#firstNameEdit").off();
$("#lastNameEdit").off();
$("#usernameEdit").off();
$("#firstNameEdit").click(enableEditFirstName);
$("#lastNameEdit").click(enableEditLastName);
$("#usernameEdit").click(enableEditUsername);

function enableEditFirstName(){
    $("#firstNameEdit").hide();
    $("#firstNameEdit").after($('<i id="firstNameEditActive" style="color: green; margin-left: 5px" class="bi bi-check2-square mb-0"></i>'));
    $("#firstNameField").hide();
    $("#firstNameField").after($('<input id="firstNameEditField" class="text-muted m-0 form-control" style="width: 70%; display: inline" value=' + $("#firstNameField").text() + '>'));
    $("#firstNameEditField").on("input", checkFirstName);
    $("#firstNameEditActive").click(function() {confirmEdit("firstName")});
}

function enableEditLastName(){
    $("#lastNameEdit").hide();
    $("#lastNameEdit").after($('<i id="lastNameEditActive" style="color: green; margin-left: 5px" class="bi bi-check2-square mb-0"></i>'));
    $("#lastNameField").hide();
    $("#lastNameField").after($('<input id="lastNameEditField" class="text-muted m-0 form-control" style="width: 70%; display: inline" value=' + $("#lastNameField").text() +'>'));
    $("#lastNameEditField").on("input", checkLastName);
    $("#lastNameEditActive").click(function() {confirmEdit("lastName")});
}

function enableEditUsername(){
    $("#usernameEdit").hide();
    $("#usernameEdit").after($('<i id="usernameEditActive" style="color: green; margin-left: 5px" class="bi bi-check2-square mb-0"></i>'));
    $("#usernameField").hide();
    $("#usernameField").after($('<input id="usernameEditField" class="text-muted m-0 form-control" style="width: 70%; display: inline" value=' + $("#usernameField").text() +'>'));
    $("#usernameEditField").on("input", checkUsername);
    $("#usernameEditActive").click(function() {confirmEdit("username")});
}

function checkFirstName() {
    let firstName = $(this).val();

    if (!/^[A-Za-zäöüÄÖÜß\s]*$/.test(firstName)) {
        $("#firstNameError").show();
        $("#firstNameEditActive").css("color", "red");
        $("#firstNameEditActive").unbind();
        return;
    }

    if(firstName.length > 50){
        $("#firstNameError").show();
        $("#firstNameEditActive").css("color", "red");
        $("#firstNameEditActive").unbind();
        return;
    }

    $("#firstNameError").hide();
    $("#firstNameEditActive").css("color", "green");
    $("#firstNameEditActive").off();
    $("#firstNameEditActive").click(function() {confirmEdit("firstName")});
}

function checkLastName() {
    let lastName = $(this).val();

    if (!/^[A-Za-zäöüÄÖÜß\s]*$/.test(lastName)) {
        $("#lastNameError").show();
        $("#lastNameEditActive").css("color", "red");
        $("#lastNameEditActive").unbind();
        return;
    }

    if (lastName.length > 50) {
        $("#lastNameError").show();
        $("#lastNameEditActive").css("color", "red");
        $("#lastNameEditActive").unbind();
        return;
    }

    $("#lastNameError").hide();
    $("#lastNameEditActive").css("color", "green");
    $("#lastNameEditActive").off();
    $("#lastNameEditActive").click(function() {confirmEdit("lastName")});
}

function checkUsername() {

    let username = $(this).val();
    if ($("#usernameField").text() === username) {
        $("#usernameError").hide();
        $("#usernameError2").hide();
        $("#usernameUnavailable").hide();
        $("#usernameAvailable").hide();
        $("#usernameEditActive").css("color", "green");
        $("#usernameEditActive").off();
        $("#usernameEditActive").click(function() {confirmEdit("username")});
        return;
    }

    if (!/^[a-zA-Z0-9äöüÄÖÜß\.]+$/.test(username)) {
        $("#usernameUnavailable").hide();
        $("#usernameAvailable").hide();
        $("#usernameError").hide();
        $("#usernameError2").show();
        $("#usernameEditActive").css("color", "red");
        $("#usernameEditActive").unbind();
        return;
    }

    if (username.length < 6 || username.length > 50) {
        $("#usernameUnavailable").hide();
        $("#usernameAvailable").hide();
        $("#usernameError2").hide();
        $("#usernameError").show();
        $("#usernameEditActive").css("color", "red");
        $("#usernameEditActive").unbind();
        return;
    }

    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "checkUserNameAvailable", user: username},
        cache: false,
        dataType: "json",
        success: function (response) {
            if (response["userNameAvailable"]) {
                $("#usernameError").hide();
                $("#usernameError2").hide();
                $("#usernameUnavailable").hide();
                $("#usernameAvailable").show();
                $("#usernameEditActive").css("color", "green");
                $("#usernameEditActive").off();
                $("#usernameEditActive").click(function() {confirmEdit("username")});
                return;
            }
            
            $("#usernameError").hide();
            $("#usernameError2").hide();
            $("#usernameAvailable").hide();
            $("#usernameUnavailable").show();
            $("#usernameEditActive").css("color", "red");
            $("#usernameEditActive").unbind();
        },
        error: function (error) {
            console.log(error);
            alert("Error checking username!");
        }
    });
}

function confirmEdit(type) {

    if (type === "username") {
        $("#usernameAvailable").hide();
    }

    //checks if data is unchanged
    if ($("#" + type + "Field").text() !== $("#" + type + "EditField").val()) {
        sendChangedInfoToServer(type, $("#" + type + "EditField").val());
    }

    $("#" + type + "EditActive").remove();
    $("#" + type + "Edit").show();
    $("#" + type + "EditField").hide();
    $("#" + type + "Field").show();
    loadUserInfo();
    checkLoginStatus();
}

function sendChangedInfoToServer(type, data) {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "updateUserData", type: type, data: data},
        cache: false,
        dataType: "json",
        success: function (response) {
            notyf.success('Änderung erfolgreich!');
        },
        error: function (error) {
            console.log(error);
            alert("Error updating user info!");
        }
    });
}


//--------------Change Password Functions---------------//

function showHideOldPw() {
    if ($("#oldPassword").attr("type") === "password") {
        $("#oldPassword").attr("type", "text");
        $("#oldPasswordShow").attr("class", "bi bi-eye-slash-fill");
        return;
    }

    $("#oldPassword").attr("type", "password");
    $("#oldPasswordShow").attr("class", "bi bi-eye-fill");
}

function showHideNewPw() {
    if ($("#newPassword").attr("type") === "password") {
        $("#newPassword").attr("type", "text");
        $("#newPasswordConfirm").attr("type", "text");
        $("#newPasswordShow").attr("class", "bi bi-eye-slash-fill");
        $("#newPasswordConfirmShow").attr("class", "bi bi-eye-slash-fill");
        return;
    }

    $("#newPassword").attr("type", "password");
    $("#newPasswordConfirm").attr("type", "password");
    $("#newPasswordShow").attr("class", "bi bi-eye-fill");
    $("#newPasswordConfirmShow").attr("class", "bi bi-eye-fill");
}

function submitPasswordChange() {

    $("#oldPasswordError").hide();
    $("#newPasswordError").hide();
    $("#newPasswordError2").hide();
    $("#newPasswordConfirmError").hide();
    
    let newPassword = $("#newPassword").val();
    let newPasswordConfirm = $("#newPasswordConfirm").val();
    let oldPassword = $("#oldPassword").val();
    

    if(newPassword.length < 6 || newPassword.length > 50){
        $("#newPasswordError").show();
        return;
    }

    if(newPassword !== newPasswordConfirm){
        $("#newPasswordConfirmError").show();
        return;
    }

    if(oldPassword === newPassword){
        $("#newPasswordError2").show();
        return;
    }

    //checks if newPassword is empty field
    if(!oldPassword){
        $("#oldPasswordError").show();
        return;
    }

    sendPasswordChangeToServer(oldPassword, newPassword);
}

function sendPasswordChangeToServer(oldPassword, newPassword) {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "updateUserPassword", old_password: oldPassword, new_password: newPassword},
        cache: false,
        dataType: "json",
        success: function (response) {
            if (response["success"]) {      
                notyf.success('Passwort erfolgreich geändert!');
                $("#changePasswordModal").modal("hide");
                $('#changePasswordForm')[0].reset();
                $('#passwordChangeSuccess').css("display", "inline");
                return;
            }
            $("#oldPasswordError").show();
        },
        error: function (error) {
            console.log(error);
            alert("Error updating user password!");
        }
    });
}

//------Enables enter key to submit password change-------

$('#changePasswordModal').on('show.bs.modal', function () {
    document.addEventListener("keydown", enterKey);
})

$('#changePasswordModal').on('hide.bs.modal', function () {
    document.removeEventListener("keydown", enterKey);
})

function enterKey(event) {
    if (event.key === "Enter") {
        $("#submitPasswordChange").click();
    }
}