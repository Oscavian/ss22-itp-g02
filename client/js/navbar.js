checkLoginStatus();

function checkLoginStatus() {
    $("#log-stat").empty();
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getLoginStatus"},
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["isLoggedIn"]){
                $("#log-stat").append("<li class='nav-item mb-1'><div class='navbar-brand'>" + response["username"] + "</div></li>");
                $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='logoutButton' class='btn btn-outline-warning me-2 fs-5' onclick='logout()'>Logout</button></li>");
                return;
            }
            
            $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='loginButton' class='btn btn-outline-warning me-2 fs-5' data-bs-toggle='modal' data-bs-target='#loginModal'>Login</button></li>");
            //login-modal and login-js is only loaded if user is not logged in
            $("login-modal").load("client/html-includes/login-modal.html");
      },
      error: function(error){
            console.log("AJAX-Request error: " + error);
            alert("Error checking login status!");
                }
    });
}

function logout(){
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "logout"},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#log-stat").empty();
            $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='loginButton' class='btn btn-outline-warning me-2 fs-5' data-bs-toggle='modal' data-bs-target='#loginModal'>Login</button></li>");
            $("login-modal").load("client/html-includes/login-modal.html");
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });
}