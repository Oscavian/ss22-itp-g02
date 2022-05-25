var groupId = new URLSearchParams(window.location.search).get("id");
console.log("Loading Group with id: " + groupId);
loadGroupDetails(groupId);

$("#group-details-content").load("client/pages/tasks/task-overview.html")
 
function groupViewShowAssignments(){
    $("#group-details-nav .nav-link").each(function() {
        $(this).removeClass("active");
    });

    $("#group-details-nav-assignments").addClass("active");

    $("#group-details-content").load("client/pages/tasks/task-overview.html")
}

function groupViewShowChat(){
    $("#group-details-nav .nav-link").each(function() {
        $(this).removeClass("active");
    });

    $("#group-details-nav-chat").addClass("active");

    $("#group-details-content").load("client/pages/chat/chat.html")
}

function groupViewShowMembers(){
    $("#group-details-nav .nav-link").each(function() {
        $(this).removeClass("active");
    });

    $("#group-details-nav-members").addClass("active");

    $("#group-details-content").load("client/pages/user/group-members.html")
}

function loadGroupDetails(groupId){
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getGroupName", group_id: groupId},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#groupTitle").text(response["groupName"]);
            $("title").text(response["groupName"]);
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });

    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getGroupTeacher", group_id: groupId},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#groupTeacher").text("Lehrer*in: " + response["teacherFirstName"] + " " + response["teacherLastName"]);
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });
}