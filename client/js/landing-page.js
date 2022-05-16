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
                    $("#landingHeadline").html("Willkommen Herr/Frau " + response["lastName"] + "!");
                    $("#landingContent").append("<h3>Neuigkeiten: </h3><ul class='list-group list-group-flush'>" + 
                    "<li><h4>Website Änderungen</h4>Unsere Website wurde überarbeitet. Unter anderem sorgt das für Sie für ein angenehmeres Erlebnis auf unserer Seite.</li>" + 
                    "<li><h4>Reperaturen im Lehrerzimmer fertiggestellt</h4></li></ul>" + 
                    "<h3 class='mt-3'>Essensplan: </h3><ul class='list-group list-group-flush'><li><h4>Heute gibt es Tomatenspaghetti und Schnitzel mit Salat</h4>Wir wünschen einen Guten Appetit</li>");
                }
                else if(response["userType"] === 2){
                    $("#landingHeadline").html("Willkommen liebe/r Schüler/in!");
                    $("#landingContent").append("<h3>Neuigkeiten:</h3><ul class='list-group list-group-flush'>" + 
                    "<li><h4>Website Änderungen</h4>Unsere Website wurde überarbeitet. Unter anderem sorgt das für dich für ein angenehmeres Erlebnis auf unserer Seite.</li>" + 
                    "<li><h4>Neuer Beamer</h4>Im Raum 102 der Klasse 1C gibt es nun einen neue Beamer, nachdem der alte kaputt gegangen ist.</li></ul>" + 
                    "<h3 class='mt-3'>Essensplan:</h3><ul class='list-group list-group-flush'><li><h4>Heute gibt es Tomatenspaghetti und Schnitzel mit Salat</h4>Wir wünschen einen Guten Appetit</li>");
                }
            }
            else{
                $("#landingHeadline").html("Willkommen!");
                $("#landingContent").append("<h3>Neuigkeiten: </h3><ul class='list-group list-group-flush'>" + 
                    "<li><h4>Website Änderungen</h4>Unsere Website wurde überarbeitet. Unter anderem sorgt das für Sie für ein angenehmeres Erlebnis auf unserer Seite.</li>" + 
                    "<h3 class='mt-3'>Essensplan: </h3><ul class='list-group list-group-flush'><li><h4>Heute gibt es Tomatenspaghetti und Schnitzel mit Salat</h4>Wir wünschen einen Guten Appetit</li>");
            }
      },
      error: function(error){
        console.log("AJAX-Request error: " + error);
        alert("Error checking login status!");
        }
    });
}