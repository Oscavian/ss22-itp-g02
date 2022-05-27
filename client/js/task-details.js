var assignmentId = new URLSearchParams(window.location.search).get("id");
loadAssignmentDetails(assignmentId);

function loadAssignmentDetails(assignmentId) {
  $.ajax({
    type: "POST",
    url: "/ss22-itp-g02/backend/requestHandler.php",
    data: { method: "getAssignmentById", assignment_id: assignmentId },
    cache: false,
    dataType: "json",
    success: function (response) {
      $("#taskTitle").text(response["title"]);
      $("title").text(response["title"]);

      $("#taskAuthor").text(response["creator_first_name"] + " " + response["creator_last_name"]);

      date = new Date(response["time"]);
      dateYear = dueDate.getFullYear() % 2000;
      dateMonth = new String(dueDate.getMonth() + 1).padStart(2, "0");
      dateDay = new String(dueDate.getDate()).padStart(2, "0");
      dateString = dateDay + "." + dateMonth + "." + dateYear;

      $("#taskTime").text(dateString);

      dueDate = new Date(response["due_time"]);
      dueDateYear = dueDate.getFullYear() % 2000;
      dueDateMonth = new String(dueDate.getMonth() + 1).padStart(2, "0");
      dueDateDay = new String(dueDate.getDate()).padStart(2, "0");
      dueDateHours = new String(dueDate.getHours()).padStart(2, "0");
      dueDateMinutes = new String(dueDate.getMinutes()).padStart(2, "0");
      dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;

      $("#taskDeadline").text("abzugeben bis " + dueDateString);

      $("#taskDescription").text(response["text"]);

      if (response["file_path"]) {
        fileName = response["file_path"].split("/").pop()
        $("#taskDescriptionFileName").text(fileName);
        $("#taskDescriptionFile").show();
        $("#taskDescriptionFile").off();
        $("#taskDescriptionFile").click(() => {
          downloadTaskDescriptionFile(assignmentId);
        });
      }

      if(response["submitted"]){

        ownSubmissionTime = new Date(response["ownSubmissionTime"]);
        ownSubmissionYear = ownSubmissionTime.getFullYear() % 2000;
        ownSubmissionMonth = new String(ownSubmissionTime.getMonth() + 1).padStart(2, "0");
        ownSubmissionDay = new String(ownSubmissionTime.getDate()).padStart(2, "0");
        ownSubmissionHours = new String(ownSubmissionTime.getHours()).padStart(2, "0");
        ownSubmissionMinutes = new String(ownSubmissionTime.getMinutes()).padStart(2, "0");
        ownSubmissionString = ownSubmissionDay + "." + ownSubmissionMonth + "." + ownSubmissionYear;
        ownSubmissionString2 = ownSubmissionHours + ":" + ownSubmissionMinutes;  

        ownSubmissionFileName = response["ownSubmissionFileName"].split("/").pop();
        $("#TaskStatus").css("color", "green");
        $("#TaskStatus").text('"' + ownSubmissionFileName + '" wurde am ' + ownSubmissionString + ' um ' + ownSubmissionString2 + ' abgegeben');
      
      } else {
        timeleft = dueDate.getTime() - new Date().getTime();
        timeleftDays = timeleft / 86400000;
        timeleftHours = timeleftDays % 1;
        timeleftDays = Math.floor(timeleftDays);
        timeleftHours = timeleftHours * 24;
        timeleftHours= Math.floor(timeleftHours);    

        $("#TaskStatus").css("color", "purple");
        $("#TaskStatus").text('Für die Abgabe sind noch ' + timeleftDays + ' Tage und ' + timeleftHours+ ' Stunden Zeit');

        $("#addSubmissionFormSpan").show();
      }



    },
    error: function (error) {
      console.log("AJAX-Request error: " + error);
    },
  });
}


function downloadTaskDescriptionFile(assignmentId) {
  window.location = "/ss22-itp-g02/backend/requestHandler.php?method=downloadAssignmentFile&assignment_id=" + assignmentId;
}

function uploadSubmission(){

  file = document.getElementById("submissionFileUploadInput").files[0];

  if(!file){
    $("#noFileError").show();
    return;
  }

  formData = new FormData();
  formData.append("attachment", file, file.name);
  formData.append("method", "addSubmission");
  formData.append("assignment_id", assignmentId);

  $.ajax({
    type: "POST",
    url: "/ss22-itp-g02/backend/requestHandler.php",
    success: function (data) {
      loadAssignmentDetails(assignmentId);
      $("#addSubmissionFormSpan").hide();
    },
    error: function (error) {
        console.log("AJAX-Request error: " + error);
    },
    async: true,
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
});
}