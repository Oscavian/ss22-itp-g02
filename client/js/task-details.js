var assignmentId = window.location.href.split('?assignmentId=').pop()
console.log("Loading assignment with id: " + assignmentId);
loadAssignmentDetails(assignmentId);

function loadAssignmentDetails(assignmentId){
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getAssignmentById", assignment_id: assignmentId},
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#taskTitle").text(response["title"]);

            $("#taskAuthor").text(response["creator_first_name"] + " " + response["creator_last_name"]);

            date = new Date(response["time"]);
            dateYear = dueDate.getFullYear() % 2000;
            dateMonth = new String(dueDate.getMonth() + 1).padStart(2, '0');
            dateDay = new String(dueDate.getDate()).padStart(2, '0');
            dateString = dateDay + "." + dateMonth + "." + dateYear;
            
            $("#taskTime").text(dateString);

            dueDate = new Date(response["due_time"]);
            dueDateYear = dueDate.getFullYear() % 2000;
            dueDateMonth = new String(dueDate.getMonth() + 1).padStart(2, '0');
            dueDateDay = new String(dueDate.getDate()).padStart(2, '0');
            dueDateHours = new String(dueDate.getHours()).padStart(2, '0');
            dueDateMinutes = new String(dueDate.getMinutes()).padStart(2, '0');
            dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;
            
            $("#taskDeadline").text("abzugeben bis " + dueDateString);

            $("#taskDescription").text(response["text"]);
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });

}