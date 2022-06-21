$("#newNameError").hide();
$("#groupAddResponse").hide();
getUserGroups();

async function getUserGroups() {
    $("#group-main-body").empty();
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){
                $("#group-main-body").empty();
                if (response["noGroups"]){
                    $("#showNewGroupForm").hide();
                    $("#group-main-body").append(`
                        <section style='background-color: #eee; border-radius: 5px; margin-top: 20px; margin-bottom: 20px; border: 2px solid purple;'>
                            <div class='container group-details-container p-4'>
                                <div class='col-lg-12'>
                                    <div style='display: flex; flex-direction: column; align-items: center; justify-content: center;'>
                                        <div style='font-weight: bold; font-size: 2em; color: purple' id='groupTitle'>Sie befinden sich in keiner Gruppe</div>
                                        <button type="button" class="glow-on-hover newform-btn buttonNewGroup" onclick="showNewGroupForm()"><img src="client/assets/img/pen.png" style="height: 25px; margin-right: 2px;"> Neue Gruppe erstellen</button>
                                    </div>
                                </div>
                            </div>
                        </section>
                        `);
                }
                else {
                    $("#showNewGroupForm").show();
                    $.each(response["groups"], (i, g) => {
                        $("#group-main-body").append("" +
                        "<section class='groupOverviewElement' style='overflow: hidden; background-color: #eee; border-radius: 5px; margin-top: 20px; margin-bottom: 20px' onclick='loadPage(`gruppe`, " + g['groupId'] + ");'>" +
                        "   <div class='container group-details-container p-4' style='overflow: hidden;' >" +
                        "       <div class='col-lg-12'>" + 
                        "           <div id='groupTitleAndTeacherDiv' style='display: flex; align-items: center;'>" + 
                        "               <div style='font-weight: bold; font-size: 2em;' id='groupTitle'>Gruppe " + g['groupName'] + "</div>" + 
                        "               <div style='margin-left: auto; color: rgb(61, 61, 61); font-weight: 500; font-size: 1em;' id='groupTeacher'>Lehrer*in: " + g['teacherFirstName'] + " " + g['teacherLastName'] + "</div>" +
                        "           </div>" + 
                        "       <div id='group-details-content-card' class='card group-details-content-card' style='margin-top: 1rem;'>" + 
                        "           <div id='group-details-content' class='card-body' style='display: flex; align-items: center'>" + 
                        "              <img src='client/assets/img/thunder.png' class='click-img' alt='hand smashing button'><div style='margin-left: 10px'>Klicken Sie hier für nähere Informationen</div></div></div></div></div></section>");
                    })
                }
            }
        },
        error: (error) => {
            console.log(error);
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
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "createGroup", group_name: newGroupName},
        cache: false,
        dataType: "json",
        success: (response) => {
            notyf.success('Die Gruppe "' + newGroupName +'" wurde erstellt!');
        },
        error: (error) => {
            console.log(error);
        },
    });
    $("#groupAddResponse").show();
    $("#new-group-body").hide();
    $("#showNewGroupForm").show();
    getUserGroups();
}