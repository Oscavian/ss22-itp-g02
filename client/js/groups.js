getUserGroups();

function getUserGroups() {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){
                if (response["noGroups"]){
                    // keine Gruppen vorhanden
                }
                else {
                    $.each(response["groups"], (i, g) => {
                        $("#group-main-body").append("" +
                        "<section style='background-color: #eee; border-radius: 5px; margin-top: 20px; margin-bottom: 20px' onclick='loadPageGroupDetails(" + g['groupId'] + ")'>" + 
                        "   <div class='container group-details-container p-4'>" + 
                        "       <div class='col-lg-12'>" + 
                        "           <div id='groupTitleAndTeacherDiv' style='display: flex; align-items: center;'>" + 
                        "               <div style='font-weight: bold; font-size: 2em;' id='groupTitle'>Gruppe " + g['groupName'] + "</div>" + 
                        "               <div style='margin-left: auto; color: rgb(61, 61, 61); font-weight: 500; font-size: 1em;' id='groupTeacher" + g['groupId'] + "'></div>" + 
                        "           </div>" + 
                        "       <div id='group-details-content-card' class='card group-details-content-card' style='margin-top: 1rem;'>" + 
                        "           <div id='group-details-content' class='card-body'>" + 
                        "               Content/etc</div></div></div></div></section>");
                        $.ajax({
                            type: "POST",
                            url: "/ss22-itp-g02/backend/requestHandler.php",
                            data: {method: "getGroupTeacher", group_id: g['groupId']},
                            cache: false,
                            dataType: "json",
                            success: (response) => {
                                if (response["success"]){
                                    $("#groupTeacher" + g['groupId'] + "").append("Lehrer*in: " + response['teacherFirstName'] + " " + response['teacherLastName'] + "");
                                }
                            },
                            error: (error) => {
                                console.log("AJAX Request Error: " + error);
                            }
                        });
                    })
                }
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });

    $("#group-main-body").attr("style", "block");
}

function addNewGroup(){
    console.log();
}