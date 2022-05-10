loadUserInfo();

function loadUserInfo() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getLoginStatus"},
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["isLoggedIn"]){
                userType = response["userType"];
                if(response["userType"] == 1){userType = "Lehrer*in";};
                if(response["userType"] == 2){userType = "Schüler*in";};
                $("#userTypeField").text(userType);
                $("#userFullNameField").text(response["firstName"] + " " + response["lastName"]);
                return;
            }
            
            alert("You are not logged in!");
      },
      error: function(error){
            console.log("AJAX-Request error: " + error);
            alert("Error checking login status!");
            }
    });
}