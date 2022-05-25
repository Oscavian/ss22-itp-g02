var assignmentId = new URLSearchParams(window.location.search).get("id");
console.log("Loading assignment with id: " + assignmentId);
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
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getAssignmentById", assignment_id: assignmentId},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#taskTitle").text(response["title"]);
            $("title").text(response["title"]);

            $("#taskAuthor").text(response["creator_first_name"] + " " + response["creator_last_name"]);
            
            dueDate = new Date(response["due_time"]);
            date = new Date(response["time"]);
            dateYear = dueDate.getFullYear() % 2000;
            dateMonth = new String(dueDate.getMonth() + 1).padStart(2, '0');
            dateDay = new String(dueDate.getDate()).padStart(2, '0');
            dateString = dateDay + "." + dateMonth + "." + dateYear;
            
            $("#taskTime").text(dateString);

            console.log(response["due_time"]);
            dueDateYear = dueDate.getFullYear() % 2000;
            dueDateMonth = new String(dueDate.getMonth() + 1).padStart(2, '0');
            dueDateDay = new String(dueDate.getDate()).padStart(2, '0');
            dueDateHours = new String(dueDate.getHours()).padStart(2, '0');
            dueDateMinutes = new String(dueDate.getMinutes()).padStart(2, '0');
            dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;
            
            $("#taskDeadline").text("abzugeben bis " + dueDateString);
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });

}