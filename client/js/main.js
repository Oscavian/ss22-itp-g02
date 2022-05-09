$("navbar").load("client/html-includes/navbar.html");
$("footer").load("client/html-includes/footer.html");

//Maybe load different page when user is allready logged in?
loadPageHome();


function loadPageHome(){
    $("#main-div").load("client/pages/basic/home.html");
    $("title").text("Home");
}

function loadPageRegister(){
    $("title").text("Registrieren");
    $("#main-div").load("client/pages/user/register.html");
}

function loadPageHelp(){
    $("title").text("Hilfe");
    $("#main-div").load("client/pages/basic/help.html");
}

function loadPageImprint(){
    $("title").text("Impressum");
    $("#main-div").load("client/pages/basic/imprint.html");
}

function loadPageContact(){
    $("title").text("Kontakt");
    $("#main-div").load("client/pages/basic/contact.html");
}