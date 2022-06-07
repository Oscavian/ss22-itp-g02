checkLoginForLanding();

function checkLoginForLanding() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getLoginStatus"},
        cache: false,
        dataType: "json",
        success: function (response) {
            if(response["isLoggedIn"]){
                if(response["userType"] === 1){
                    //load group overview
                    loadPage('gruppen');
                }
                else if(response["userType"] === 2){
                   //load group detail view
                   getStudentGroupId();
                }
            }
            else{
                $("#landingHeadline").html("Willkommen auf der ... Seite!");
                $("#landingContent").html("\
                <section style='background-color: #eee; border-radius: 5px; width: 75%; margin-left: auto; margin-right: auto;'>\
                    <div class='container p-4 text-dark'>\
                        <div class='text-center'><div id='mid-heading'>Die Nr.1 Plattform für Volksschulen!</div>\
                            <div class='card'>\
                                <div class='card-body text-center'><div class= 'sm-heading'> Hab alles im Überblick durch: </div><br>\
                                    <div class='row' id='overview'>\
                                        <div class='col-sm-4'>Klassenverwaltung</div>\
                                        <div class='col-sm-4'>Übungsabgaben</div>\
                                        <div class='col-sm-4'>Chatfunktion</div>\
                                    </div>\
                                    <div class='row'>\
                                        <div class='col-sm-4'><img src='client/assets/img/notebook.png' alt='klassenverwaltung-icon'></div>\
                                        <div class='col-sm-4'><img src='client/assets/img/exam.png' alt='übungsabgaben-icon'></div>\
                                        <div class='col-sm-4'><img src='client/assets/img/talk.png' alt='chatfunktion-icon'></div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div><br>\
                        <div class='card'>\
                            <div class='card-body text-center'><div class='sm-heading'>Gleich loslegen und ... nutzen?</div><br>\
                                Einfach hier klicken zum Einloggen:<br><br>\
                                <div type='button' id='loginButton' class='glow-on-hover' data-bs-toggle='modal' data-bs-target='#loginModal'>Login</div><br>\
                                <img src='client/assets/img/thunder.png' id='hit-button-img' alt='hit-button-icon'><br><br><br><hr class='hrclass'>\
                                <div>Sie sind Lehrer*in und haben noch keinen Account?</div>\
                                <div>Registrieren Sie sich <a href='#' id='register-link' onclick='loadPage(`registrieren`)'>HIER</a></div>\
                            </div>\
                        </div>\
                    </div>\
                </section>");
                $("login-modal").load("client/html-includes/login-modal.html");
                //btn btn-warning me-2 fs-5 button classes
            }
      },
      error: function(error){
        console.log("AJAX-Request error: " + error);
        alert("Error checking login status!");
        }
    });
}
