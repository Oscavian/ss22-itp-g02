//makes all ajax-calls async
$.ajaxPrefilter(function( options, original_Options, jqXHR ) {
    options.async = true;
});

//load navbar and footer
$("#indexNavbar").load("client/html-includes/navbar.html");
$("#indexFooter").load("client/html-includes/footer.html");

//-----------------Pages-----------------//

const pages = {
    home: {
        title: "Home",
        path: "client/pages/basic/home.html"
        },
    registrieren: {
        title: "Registrieren",
        path: "client/pages/user/register.html"
        },
    hilfe: {
        title: "Hilfe",
        path: "client/pages/basic/help.html"
        },
    impressum: {
        title: "Impressum",
        path: "client/pages/basic/imprint.html"
        },
    kontakt: {
        title: "Kontakt",
        path: "client/pages/basic/contact.html"
        },
    account: {
        title: "Mein Account",
        path: "client/pages/user/account-info.html"
        },
    gruppen: {
        title: "Gruppenübersicht",
        path: "client/pages/groups/groups-overview.html"
        },
    accountErstellen: {
        title: "SchülerInnenaccount erstellen",
        path: "client/pages/user/create-student-account.html"
        },
    aufgabe: {
        title: null, //title is set to assignment name later
        path: "client/pages/tasks/task-details.html"
        },
    aufgabeMitAbgaben: {
        title: null, //title is set to assignment name later
        path: "client/pages/tasks/tasks-teacher-view/submitted-tasks.html"
        },
    gruppe: {
        title: null, //title is set to group name later
        path: "client/pages/groups/group-details.html"
        },
    neueAufgabe: {
        title: "Neue Aufgabe erstellen",
        path: "client/pages/tasks/create-task.html"
        },
    seiteNichtGefunden: {
        title: "Seite nicht gefunden",
        path: "client/pages/basic/pageNotFound.html"
        },

        //add more pages here
}

//----------------First Page Load Handler-----------------//

pageName = new URLSearchParams(window.location.search).get("seite");
if(!pageName){
    loadPageWithAnimation(pages["home"].path);
    $("title").text(pages["home"].title);
} else {
    page = pages[pageName];    
    if(page){
        loadPageWithAnimation(page.path);
        if(page.title){
            $("title").text(page.title);
        }
    } else {
        loadPageWithAnimation(pages["seiteNichtGefunden"].path);
        $("title").text(pages["seiteNichtGefunden"].title);
    }    
}

//----------------Functions-----------------//

function loadPage(pageName, id = null){
    page = pages[pageName];

    if(!page){
        console.log("Error - requested page does not exist!");
    }
    
    if(page.title){
        $("title").text(page.title);
    }

    if(id){
        id = 'id=' + id;
    }

    loadPageWithAnimation(page.path);
    addState(pageName, id);
}

function loadPageWithAnimation(path){
    $("#indexContent").fadeOut("fast", function(){
        $("#indexContent").load(path, function(){
            $("#indexContent").fadeIn("fast");
        })
    });
}

function addState(pageName, id = null){
    urlInfo = "?seite=" + pageName;
    if(id){
        urlInfo += "&" + id;
    }
    window.history.pushState(null, "", urlInfo);
}

//----------------Page Forward/Back Handler-----------------//

window.onpopstate = function(event) {
    $('.modal').modal('hide');
    
    pageName = new URLSearchParams(window.location.search).get("seite");

    if(!pageName){
        loadPageWithAnimation(pages["home"].path);
        $("title").text(pages["home"].title);
        return;
    }
    
    page = pages[pageName];
    if(page){
        loadPageWithAnimation(page.path);
        if(page.title){
            $("title").text(page.title);
        }
    } else {
        loadPageWithAnimation(pages["seiteNichtGefunden"].path);
        $("title").text(pages["seiteNichtGefunden"].title);
    } 
}