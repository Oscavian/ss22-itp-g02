$(document).ready(function() {
    $("#submit").click(function () {
        checkInput();
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
    $("#showConf").change(function(){
        if($(this).is(':checked')){
         $("#confirmPassword").attr("type","text");
         $("#showConfText").text("Hide");
        }else{
         // Changing type attribute
         $("#confirmPassword").attr("type","password");
        
         // Change the Text
         $("#showConfText").text("Show");
        }  
    });
})

function checkInput() {

    emptyErrors();
    var allOk = true;
    allOk = checkIfEmpty(allOk);
    allOk = checkIfAlphabet(allOk);
    allOk = checkIfAlphanumeric(allOk);
    allOk = checkLength(allOk);
    allOk = checkPw(allOk);

    if(allOk === true) {
        submitInput();
    }
}

function checkIfEmpty(allIsOk) {
    var allOk = allIsOk;
    if($("#first_name").val() === ""){
        allOk = false;
        $("#first_name_error").append("Please enter a first name <br>");
    }
    if ($("#last_name").val() === ""){
        allOk = false;
        $("#last_name_error").append("Please enter a last name <br>");
    }
    if($("#user").val() === ""){
        allOk = false;
        $("#user_error").append("Please enter a username <br>");
    }
    if($("#password").val() === ""){
        allOk = false;
        $("#password_error").append("Please enter a password <br>");
    }
    if($("#confirmPassword").val() === ""){
        allOk = false;
        $("#confirm_password_error").append("Please confirm your password <br>");
    }
    return allOk;
}

function checkIfAlphabet(allIsOk){
    //checks if first and last name consist of letters only
    var allOk = allIsOk;
    var firstName = $("#first_name").val();
    var lastName = $("#last_name").val();
    if(!/^[A-Za-z\s]*$/.test(firstName)){
        allOk = false;
        $("#first_name_error").append("First name must consist of letters only <br>");
    }
    if(!/^[A-Za-z\s]*$/.test(lastName)){
        allOk = false;
        $("#last_name_error").append("Last name must consist of letters only <br>");
    }
    return allOk;
}

function checkIfAlphanumeric(allIsOk){
    var allOk = allIsOk;
    var user = $("#user").val();
    var password = $("#password").val();
    if(!/^[a-zA-Z0-9]+$/.test(user)){
        allOk = false;
        $("#user_error").append("Username must consist of letters and numbers only <br>");
    }
    if(!/^[a-zA-Z0-9]+$/.test(password)){
        allOk = false;
        $("#password_error").append("Password must consist of letters and numbers only <br>");
    }
    return allOk;
}

function checkLength(allIsOk){
    var allOk = allIsOk;
    if($("#first_name").val().length > 50){
        allOk = false;
        $("#first_name_error").append("Only a maximum of 50 characters allowed <br>");
    }
    if($("#last_name").val().length > 50){
        allOk = false;
        $("#last_name_error").append("Only a maximum of 50 characters allowed <br>");
    }
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

function checkPw(allIsOk){
    //checks if password and confirmation are the same
    var allOk = allIsOk;
    var pw = $("#password").val();
    var cpw = $("#confirmPassword").val();
    if(pw !== cpw){
        $("#password_error").append("Password and its confirmation must match <br>");
        allOk = false;
    }
    return allOk;
}

function submitInput() {
    $.post("backend/requestHandler.php", {
        method: "registerTeacher",
        first_name: $("#first_name").val(),
        last_name: $("#last_name").val(),
        user: $("#user").val(),
        password: $("#password").val(),
    }, 
    function(response) {
        if(response["userNameUnavailable"] === true){
            $("#post-response").text("The username chosen is unavailable");
        }
        if(response["unknownError"] === true){
            $("#post-response").text("An error occurred while processing the request");
        }
        if(response["success"] === true){
            $("#post-response").text("Your account was successfully created");
            $('#register-form')[0].reset();
        }
        if(response["formDataInvalid"] === true){
            $("#post-response").text("The data entered is invalid");
        }
        
    });
}

function emptyErrors(){
    $("#first_name_error").empty();
    $("#last_name_error").empty();
    $("#user_error").empty();
    $("#password_error").empty();
    $("#confirm_password_error").empty();
}