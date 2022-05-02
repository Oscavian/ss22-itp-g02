$(document).ready(function() {
    //path to html-file that should be included
    fetch("/ss22-itp-g02/client/html-includes/navbar.html")
    .then(response => {
        return response.text()
    })
    .then(data => {
        //query-selector in DOM fetched file-content should be placed in
        document.querySelector("navbar").innerHTML = data;
    }); 

    waitForNavbar();
    
    fetch("/ss22-itp-g02/client/html-includes/footer.html")
    .then(response => {
        return response.text()
    })
    .then(data => {
        document.querySelector("footer").innerHTML = data;
    }); 
    //works with any kind of HTML-content (also with bootstrap included); 
    //query selector used must be in DOM
});
    
function waitForNavbar() {
    if ($( "#navcolor" ).length) {
        checkLoginStatus();
    } else {
      setTimeout(function() {
        waitForNavbar();
      }, 100);
    }
  };

  function checkLoginStatus() {
      $("#log-stat").empty();
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: jQuery.param({
            method: "getLoginStatus", 
        }),
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["isLoggedIn"] === true){
                //$("#post-response").append("You are now logged in with username " + response["username"] + ".");
                $("#log-stat").append("<li class='nav-item mb-1'><div class='navbar-brand'>"+response["username"] + "</div></li>");
                $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='logout' class='btn btn-warning me-2 fs-5' onclick='logoutUser()'>Logout</button></li>");
            }
            else if(response["isLoggedIn"] === false){
                $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='login' class='btn btn-warning me-2 fs-5' data-bs-toggle='modal' data-bs-target='#loginModal' onclick='waitForModal()'>Login</button></li>");
            }

        },
        error: function(error){
            alert("Oops, something went wrong. Please try again!");
            $("#log-stat").append("<li class='nav-item mb-1'><button type='button' id='login' class='btn btn-warning me-2 fs-5' data-bs-toggle='modal' data-bs-target='#loginModal' onclick='waitForModal()'>Login</button></li>");

        }
    });
}