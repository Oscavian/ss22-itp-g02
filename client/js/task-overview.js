var groupId = window.location.href.split('?groupId=').pop()
console.log("Loading tasks for group with id: " + groupId);
loadTaskOverview(groupId);

function loadTaskOverview(groupId) {
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getGroupAssignments", group_id: groupId},
        cache: false,
        dataType: "json",
        success: function (response) {
            
            currentAssignments = [];
            pastAssignments = [];

            response["groupAssignments"].forEach((element) => {
                dueDate = new Date(element["due_time"]);
                if(new Date().getTime() > dueDate.getTime()){
                    pastAssignments.push(element);
                }else{
                    currentAssignments.push(element);
                }
            });

            if(currentAssignments.length){
                if(isTeacher){
                    $("#addNewAssignmentCard").show();
                }
                currentAssignments.forEach(displayCurrentAssignment);  
            } else {       
                if(isTeacher){
                    $("#noCurrentTasksMessageTeacher").show();
                } else {
                    $("#noCurrentTasksMessageStudent").show();
                }
            }
            
            if(pastAssignments.length){
                pastAssignments.reverse();
                pastAssignments.forEach(displayPastAssignment);
            } else {
                $("#noOldTasksMessage").show();
            }
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });
}

function displayCurrentAssignment(assignment){
    
    dueDate = new Date(assignment.due_time);
    timeleft = dueDate.getTime() - new Date().getTime();
    timeleftDays = timeleft / 86400000;
    timeleftHours = timeleftDays % 1;
    timeleftDays = Math.floor(timeleftDays);
    timeleftHours = timeleftHours * 24;
    timeleftHours= Math.floor(timeleftHours);

    dueDateYear = dueDate.getFullYear() % 2000;
    dueDateMonth = new String(dueDate.getMonth() + 1).padStart(2, '0');
    dueDateDay = new String(dueDate.getDate()).padStart(2, '0');
    dueDateHours = new String(dueDate.getHours()).padStart(2, '0');
    dueDateMinutes = new String(dueDate.getMinutes()).padStart(2, '0');
    dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;

    card = document.createElement("div");
    $(card).addClass("card");
    $(card).addClass("assignmentCard");
    $(card).html(`
    <div class="card-body">
        <h5  class="card-title assignmentCardTitle">${assignment.title}</h5>
        <h6 class="card-subtitle  mb-2 text-muted assignmentCardTime"><i class="bi bi-calendar-event" style="margin-right: 5px; vertical-align:top; font-size: 15px;"></i>${dueDateString}</h6>
        <p class="card-text assignmentCardText">${assignment.text}</p>
        </div>
    <h6 class="card-footer mb-0 text-muted assignmentCardTimeLeft"><i class="bi bi-alarm-fill" style="color: purple; margin-right: 5px; vertical-align:baseline; font-size: 15px;"></i>${timeleftDays} Tage, ${timeleftHours} Stunden</h6>
    `)
    
    $("#currentAssignmentsContent").prepend(card);

    if(isTeacher){
        $(card).click(function() {
            loadPageAssignmentSubmits(assignment.assignmentId);
        });
    } else {
        $(card).click(function() {
            loadPageAssignmentDetails(assignment.assignmentId);
        });
    }
}

function displayPastAssignment(assignment){
    
    dueDate = new Date(assignment.due_time);
    timeleft = dueDate.getTime() - new Date().getTime();
    timeleftDays = timeleft / 86400000;
    timeleftHours = timeleftDays % 1;
    timeleftDays = Math.floor(timeleftDays);
    timeleftHours = timeleftHours * 24;
    timeleftHours= Math.floor(timeleftHours);

    dueDateYear = dueDate.getFullYear() % 2000;
    dueDateMonth = new String(dueDate.getMonth() + 1).padStart(2, '0');
    dueDateDay = new String(dueDate.getDate()).padStart(2, '0');
    dueDateHours = new String(dueDate.getHours()).padStart(2, '0');
    dueDateMinutes = new String(dueDate.getMinutes()).padStart(2, '0');
    dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;

    card = document.createElement("div");
    $(card).addClass("card");
    $(card).addClass("assignmentCard");
    $(card).html(`
    <div class="card-body">
        <h5  class="card-title assignmentCardTitle">${assignment.title}</h5>
        <h6 class="card-subtitle  mb-2 text-muted assignmentCardTime"><i class="bi bi-calendar-event" style="margin-right: 5px; vertical-align:top; font-size: 15px;"></i>${dueDateString}</h6>
        <p class="card-text assignmentCardText">${assignment.text}</p>
        </div>
    <h6 class="card-footer mb-0 text-muted assignmentCardTimeLeft"><i class="bi bi-alarm-fill" style="color: purple; margin-right: 5px; vertical-align:baseline; font-size: 15px;"></i>Vor ${-timeleftDays} Tagen</h6>
    `)
    
    $("#pastAssignmentsContent").prepend(card);
    
    if(isTeacher){
        $(card).click(function() {
            loadPageAssignmentSubmits(assignment.assignmentId);
        });
    } else {
        $(card).click(function() {
            loadPageAssignmentDetails(assignment.assignmentId);
        });
    }
}