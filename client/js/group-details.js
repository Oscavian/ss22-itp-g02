
var groupId = window.location.href.split('?groupId=').pop()
console.log("Loading Group with id:" + groupId);

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
