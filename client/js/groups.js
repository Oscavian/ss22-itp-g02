$("#newNameError").hide();
$("#groupAddResponse").hide();
getUserGroups();

function getUserGroups() {
    $("#group-main-body").empty();
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){
                if (response["noGroups"]){
                    $("#group-main-body").append("" +
                        "<section style='background-color: #eee; border-radius: 5px; margin-top: 20px; margin-bottom: 20px'>" + 
                        "   <div class='container group-details-container p-4'>" + 
                        "       <div class='col-lg-12'>" + 
                        "           <div id='groupTitleAndTeacherDiv' style='display: flex; align-items: center;'>" + 
                        "               <div style='font-weight: bold; font-size: 2em; color: red' id='groupTitle'>Sie befinden sich in keiner Gruppe</div>" + 
                        "               <div style='margin-left: auto; color: rgb(61, 61, 61); font-weight: 500; font-size: 1em;' id='groupTeacherId'></div>" + 
                        "           </div>" + 
                        "       <div id='group-details-content-card' class='card group-details-content-card' style='margin-top: 1rem;'>" + 
                        "           <div id='group-details-content' class='card-body'>" + 
                        "               keine Gruppe vorhanden</div></div></div></div></section>");
                }
                else {
                    $.each(response["groups"], (i, g) => {
                        $("#group-main-body").append("" +
                        "<section style='background-color: #eee; border-radius: 5px; margin-top: 20px; margin-bottom: 20px' onclick='loadPage(`gruppe`, " + g['groupId'] + ");'>" + 
                        "   <div class='container group-details-container p-4'>" + 
                        "       <div class='col-lg-12'>" + 
                        "           <div id='groupTitleAndTeacherDiv' style='display: flex; align-items: center;'>" + 
                        "               <div style='font-weight: bold; font-size: 2em;' id='groupTitle'>Gruppe " + g['groupName'] + "</div>" + 
                        "               <div style='margin-left: auto; color: rgb(61, 61, 61); font-weight: 500; font-size: 1em;' id='groupTeacher'>Lehrer*in: " + g['teacherFirstName'] + " " + g['teacherLastName'] + "</div>" + 
                        "           </div>" + 
                        "       <div id='group-details-content-card' class='card group-details-content-card' style='margin-top: 1rem;'>" + 
                        "           <div id='group-details-content' class='card-body'>" + 
                        "               Content/etc</div></div></div></div></section>");
                    })
                }
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });

    $("#group-main-body").attr("style", "block");
    $("#showNewGroupForm").attr("style", "block");
}

function showNewGroupForm(){
    $("#new-group-body").attr("style", "block");
    $("#showNewGroupForm").hide();
    $("#groupAddResponse").hide();
}

function addNewGroup(){
    if(!($("#newGroupTitle").val())){
        $("#newNameError").show();
        return;
    }
    $("#newNameError").hide();
    let newGroupName = $("#newGroupTitle").val();
    $('#newGroupTitle').val('');
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "createGroup", group_name: newGroupName},
        cache: false,
        dataType: "json",
        success: (response) => {
            $("#groupAddResponse").text("Die Gruppe " + newGroupName + " wurde erfolgreich angelegt!");
            $("#groupAddResponse").attr("style", "font-weight: bold");
        },
        error: (e) => {
            $("#groupAddResponse").text("Die Gruppe konnte nicht angelegt werden!");
            $("#groupAddResponse").attr("style", "color: red; font-weight: bold");
        },
    });
    $("#groupAddResponse").show();
    $("#new-group-body").hide();
    $("#showNewGroupForm").show();
    getUserGroups();
}