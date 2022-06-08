//makes all ajax-calls async
$.ajaxPrefilter(function(options) {
    options.async = true;
});

/**
 * The projects' root path used to specify a sub-folder, the app resides in
 *
 * primarily used for AJAX requests
  * @type {string}
 */
const rootPath = "http://localhost/ss22-itp-g02"

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

//----------------Functions-----------------//

async function loadPage(pageName, id = null, addStateVar = true){
    
    page = pages[pageName];
    if(!page){
        loadPageWithAnimation(pages["seiteNichtGefunden"].path);
        $("title").text(pages["seiteNichtGefunden"].title);
        return;
    }

    //redirect to home-page if user is not logged in
    if(pageName != "home" && pageName != "hilfe" && pageName != "impressum" && pageName != "kontakt" && pageName != "registrieren" && !(await checkIsLoggedIn())){
        loadDefaultPage();
        return;
    }
        
    if(page.title){
        $("title").text(page.title);
    }

    if(addStateVar){
        addState(pageName, id);
    }

    loadPageWithAnimation(page.path);
}

async function loadDefaultPage(){

    if(await checkIsLoggedIn()){
        
        if(await checkIsTeacher()){
            loadPage("gruppen", null, false);
            return;
        };

        groupId = await getFirstUserGroupId();
        replaceState("gruppe", groupId);
        loadPage("gruppe", null, false);
        return;
    };
    
    replaceState("home", null);
    loadPage("home", null, false);
}

function loadPageWithAnimation(path){
    $("#indexContent").find("*").off(); //removes all current eventHandlers in content div
    $("#indexContent").fadeOut("fast", function(){
        $("#indexContent").load(path, function(){
            $("#indexContent").fadeIn("fast");
        })
    });
}

function addState(pageName, id = null){
    urlInfo = "?seite=" + pageName;
    if(id){
        id = 'id=' + id;
        urlInfo += "&" + id;
    }
    window.history.pushState(null, "", urlInfo);
}

function replaceState(pageName, id = null){
    urlInfo = "?seite=" + pageName;
    if(id){
        id = 'id=' + id;
        urlInfo += "&" + id;
    }
    window.history.replaceState(null, null, urlInfo);
}

function checkIsTeacher() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: "POST",
            url: rootPath + "/backend/requestHandler.php",
            data: {method: "getLoginStatus"},
            cache: false,
            dataType: "json",
            success: function (response) {
                if(!response["isLoggedIn"]){
                    resolve(false);
                    return;
                }

                if(response["userType"] === 1){
                    resolve(true);
                    return;
                }

                resolve(false);           
            },
            error: function (error) {
                console.log(error);
                alert("Error checking user type!");
            }
        });
    });
};

function checkIsLoggedIn() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: "POST",
            url: rootPath + "/backend/requestHandler.php",
            data: {method: "getLoginStatus"},
            cache: false,
            dataType: "json",
            success: function (response) {
                if(response["isLoggedIn"]){
                    resolve(true);
                    return;
                }

                resolve(false);
                return;           
            },
            error: function (error) {
                console.log(error);
                alert("Error checking login status!");
            }
        });
    });
};

function getFirstUserGroupId() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: "POST",
            url: rootPath + "/backend/requestHandler.php",
            data: {method: "getUserGroups"},
            cache: false,
            dataType: "json",
            success: function (response) {
                if(response["noGroups"]){
                    alert("Error loading group!");
                    return;
                }
                resolve(response["groups"][0]["groupId"]);
                return;           
            },
            error: function (error) {
                console.log(error);
                alert("Error getting group id!");
            }
        });
    });
};

//----------------Initialize on first load-----------------//

initialize();

async function initialize(){

    //load footer
    $("#indexFooter").load("client/html-includes/footer.html");

    //----------------First Page Load Handler-----------------//

    pageName = new URLSearchParams(window.location.search).get("seite");
    if(!pageName || pageName === "home"){
        loadDefaultPage();
    } else {
        loadPage(pageName, null, false);
    }
        
}

//----------------Page Forward/Back Handler-----------------//

window.onpopstate = function(event) {
    $('.modal').modal('hide');
    
    pageName = new URLSearchParams(window.location.search).get("seite");

    if(!pageName || pageName === "home"){
        loadDefaultPage();
    } else {
        loadPage(pageName, null, false);
    }
}