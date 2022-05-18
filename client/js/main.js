//load navbar and footer
$("#indexNavbar").load("client/html-includes/navbar.html");
$("#indexFooter").load("client/html-includes/footer.html");

//Maybe load different page when user is allready logged in?
//loadPageWithAnimation("client/pages/basic/home.html");

loadPageAssignmentDetails();
//loadPageCreateAssignment();
//loadPageAssignmentSubmits();

//--------------Pages--------------//

function loadPageHome(){
    title = "Home";
    path = "client/pages/basic/home.html";
    loadPage(title, path);
}

function loadPageRegister(){
    title = "Registrieren";
    path = "client/pages/user/register.html";
    loadPage(title, path);
}

function loadPageHelp(){
    title = "Hilfe";
    path = "client/pages/basic/help.html";
    loadPage(title, path);
}

function loadPageImprint(){
    title = "Impressum";
    path = "client/pages/basic/imprint.html";
    loadPage(title, path);
}

function loadPageContact(){
    title = "Kontakt";
    path = "client/pages/basic/contact.html";
    loadPage(title, path);
}

function loadPageUserDetails(){
    title = "Mein Account";
    path = "client/pages/user/account-info.html";
    loadPage(title, path);
}

function loadPageGroupOverview() {
    title = "Gruppenübersicht";
    path = "client/pages/groups/groups-overview.html";
    loadPage(title, path);
}

function loadPageCreateStudent(){
    title = "SchülerInnenaccount erstellen"
    path = "client/pages/user/create-student-account.html";
    loadPage(title, path);
}

function loadPageAssignmentDetails(){
    title = "Aufgabe"
    path = "client/pages/tasks/task-details.html";
    loadPage(title, path);
}

function loadPageAssignmentSubmits(){
    title = "Abgabenübersicht"
    path = "client/pages/tasks/submitted-tasks.html";
    loadPage(title, path);
}

function loadPageCreateAssignment(){
    title = "Neue Aufgabe erstellen"
    path = "client/pages/tasks/create-task.html";
    loadPage(title, path);
}


//add more pages here

//----------------Functions-----------------//

function loadPage(title, path){
    $("title").text(title);
    loadPageWithAnimation(path);
    addState(title, path);
}

function loadPageWithAnimation(path){
    $("#indexContent").fadeOut("fast", function(){
        $("#indexContent").load(path, function(){
            $("#indexContent").fadeIn("fast");
        })
    });
}

function addState(title, path){
    stateObject = {"pagePath": path, "pageTitle": title};
    window.history.pushState(stateObject, "");
}

window.onpopstate = function(event) {
    $('.modal').modal('hide');
    if(event.state == null){
        loadPageWithAnimation("client/pages/basic/home.html");
        $("title").text("Home");
        return;
    }
    loadPageWithAnimation(event.state.pagePath);
    $("title").text(event.state.pageTitle);
}