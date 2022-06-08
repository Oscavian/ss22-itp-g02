var assignmentId = new URLSearchParams(window.location.search).get("id");
loadAssignmentDetails(assignmentId);

$("#submitted-tasks-content").load("client/pages/tasks/tasks-teacher-view/submitted-task-details.html");
 
 function submittedTasksShowDetails(){
    $("#submitted-tasks-nav .nav-link").each(function() {
        $(this).removeClass("active");
    });

    $("#submitted-tasks-nav-details").addClass("active");

    $("#submitted-tasks-content").load("client/pages/tasks/tasks-teacher-view/submitted-task-details.html")
}

function submittedTasksShowSubmissions(){
    $("#submitted-tasks-nav .nav-link").each(function() {
        $(this).removeClass("active");
    });

    $("#submitted-tasks-nav-submissions").addClass("active");

    $("#submitted-tasks-content").load("client/pages/tasks/tasks-teacher-view/submitted-task-submissions.html")
}

function loadAssignmentDetails(assignmentId){
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data: {method: "getAssignmentById", assignment_id: assignmentId},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#taskTitle").text(response["title"]);
            $("title").text(response["title"]);

            $("#taskAuthor").text(response["creator_first_name"] + " " + response["creator_last_name"]);
            
            let dueDate = new Date(response["due_time"]);
            let date = new Date(response["time"]);
            let dateYear = dueDate.getFullYear() % 2000;
            let dateMonth = String(dueDate.getMonth() + 1).padStart(2, '0');
            let dateDay = String(dueDate.getDate()).padStart(2, '0');
            let dateString = dateDay + "." + dateMonth + "." + dateYear;
            
            $("#taskTime").text(dateString);

            let dueDateYear = dueDate.getFullYear() % 2000;
            let dueDateMonth = String(dueDate.getMonth() + 1).padStart(2, '0');
            let dueDateDay = String(dueDate.getDate()).padStart(2, '0');
            let dueDateHours = String(dueDate.getHours()).padStart(2, '0');
            let dueDateMinutes = String(dueDate.getMinutes()).padStart(2, '0');
            let dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;
            
            $("#taskDeadline").text("abzugeben bis " + dueDateString);
        },
        error: function(error){
            console.log(error);

        }
    });

}