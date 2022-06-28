checkLoginStatus();

function checkLoginStatus() {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "getLoginStatus"},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#log-stat").empty();
            if(response["isLoggedIn"]){
                $("#footerUserName").text(response["username"]);
                $("#footer-account").show();
                return;
            }
        },
        error: function(error){
                console.log(error);
                alert("Error checking login status!");
                    }
    });
}

function logout() {
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "logout"},
        cache: false,
        dataType: "json",
        success: function (response) {
            notyf.success('Logout erfolgreich!');
            $("#footer-account").hide();
            loadPage('home');
        },
        error: function(error){
            console.log(error);
        }
    });
}