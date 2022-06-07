checkLoginStatus();

var isTeacher; //global Variable, used for showing/hiding elements later, set in checkLoginStatus()

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
                isTeacher = response["userType"] === 1;
                $("#log-stat").append("<li class='nav-item mb-1'><div type='button' class='btn btn-outline-warning me-2 fs-5' onclick='loadPage(`account`);'>" + response["username"] + "</div></li>");
                $("#log-stat").append("<li class='nav-item mb-1'><div type='button' id='logoutButton' class='btn btn-outline-warning me-2 fs-5' onclick='logout()'>Logout</div></li>");
                $("#myGroupsNavButton").show();
                return;
            }
            $("#log-stat").append("<li class='nav-item mb-1'><div type='button' id='loginButton' class='btn btn-outline-warning me-2 fs-5' onclick='loadPage(`registrieren`)'>Registrieren</div></li>");
            $("#log-stat").append("<li class='nav-item mb-1'><div type='button' id='loginButton' class='btn btn-outline-warning me-2 fs-5' data-bs-toggle='modal' data-bs-target='#loginModal'>Login</div></li>");
            //login-modal and login-js is only loaded if user is not logged in
            $("login-modal").load("client/html-includes/login-modal.html");
        },
        error: function (error) {
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
            $("#log-stat").empty();
            $("#log-stat").append("<li class='nav-item mb-1'><div type='button' id='loginButton' class='btn btn-outline-warning me-2 fs-5' onclick='loadPage(`registrieren`)'>Registrieren</div></li>");
            $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='loginButton' class='btn btn-outline-warning me-2 fs-5' data-bs-toggle='modal' data-bs-target='#loginModal'>Login</button></li>");
            $("login-modal").load("client/html-includes/login-modal.html");
            $("#homeworkNavButton").hide();
            $("#chatNavButton").hide();
            $("#myGroupsNavButton").hide();
            $("#createStudentNavButton").hide();
            loadPage('home');
        },
        error: function (error) {
            console.log(error);
        }
    });
}