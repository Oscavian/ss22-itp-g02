checkLoginStatus(null);

var isTeacher;

function checkLoginStatus(val) {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getLoginStatus"},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#log-stat").empty();
            if(response["isLoggedIn"]){
                if(response["userType"] == 1){isTeacher = true;} else {isTeacher = false;}
                $("#log-stat").append("<div style='font-size: large'>Account-Info: <div type='button' class='btn btn-warning me-2 fs-5' onclick='loadPage(`account`);'>" + response["username"] + "</div>\
                 <div type='button' id='logoutButton' class='btn btn-outline-warning me-2 fs-5' onclick='logout()'>Logout</div></div>");
                 if(val === "login"){
                    if(response["userType"] === 1){
                        //load group overview
                        loadRelevantPage(null);
                    }
                    else if(response["userType"] === 2){
                       //load group detail view
                       loadRelevantPage(0);
                    }
                }
            }
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
            loadPage('home');
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });
}

function getStudentGroupId() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){
                if (!response["noGroups"]){
                    //only 1 for students
                    $.each(response["groups"], (i, g) => {
                        loadPage('gruppe', g["groupId"]);
                    });
                }
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });
}

function loadRelevantPage(cont){
    if(cont === null){
        loadPage('gruppen');
    }
    else{
        getStudentGroupId();
    }
}