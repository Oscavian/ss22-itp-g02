var assignmentId = new URLSearchParams(window.location.search).get("id");
loadAssignmentDetails(assignmentId);

function loadAssignmentDetails(assignmentId) {
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

            let date = new Date(response["time"]);
            let dueDate = new Date(response["due_time"]);

            let dateYear = dueDate.getFullYear() % 2000;
            let dateMonth = String(dueDate.getMonth() + 1).padStart(2, "0");
            let dateDay = String(dueDate.getDate()).padStart(2, "0");

            let dateString = dateDay + "." + dateMonth + "." + dateYear;

            $("#taskTime").text(dateString);
            let dueDateYear = dueDate.getFullYear() % 2000;
            let dueDateMonth = String(dueDate.getMonth() + 1).padStart(2, "0");
            let dueDateDay = String(dueDate.getDate()).padStart(2, "0");
            let dueDateHours = String(dueDate.getHours()).padStart(2, "0");
            let dueDateMinutes = String(dueDate.getMinutes()).padStart(2, "0");
            let dueDateString = dueDateDay + "." + dueDateMonth + "." + dueDateYear + " - " + dueDateHours + ":" + dueDateMinutes;

            $("#taskDeadline").text("abzugeben bis " + dueDateString);

            $("#taskDescription").text(response["text"]);

            if (response["file_path"]) {
              let fileName = response["file_path"].split("/").pop()
              $("#taskDescriptionFileName").text(fileName);
              $("#taskDescriptionFile").show();
              $("#taskDescriptionFile").off();
              $("#taskDescriptionFile").click(() => {
                downloadTaskDescriptionFile(assignmentId);
              });
            }

            if (response["submitted"]) {

                let ownSubmissionTime = new Date(response["ownSubmissionTime"]);
                let ownSubmissionYear = ownSubmissionTime.getFullYear() % 2000;
                let ownSubmissionMonth = String(ownSubmissionTime.getMonth() + 1).padStart(2, "0");
                let ownSubmissionDay = String(ownSubmissionTime.getDate()).padStart(2, "0");
                let ownSubmissionHours = String(ownSubmissionTime.getHours()).padStart(2, "0");
                let ownSubmissionMinutes = String(ownSubmissionTime.getMinutes()).padStart(2, "0");
                let ownSubmissionString = ownSubmissionDay + "." + ownSubmissionMonth + "." + ownSubmissionYear;
                let ownSubmissionString2 = ownSubmissionHours + ":" + ownSubmissionMinutes;

                let ownSubmissionFileName = response["ownSubmissionFileName"].split("/").pop();
                $("#TaskStatus").css("color", "green");
                $("#TaskStatus").text('"' + ownSubmissionFileName + '" wurde am ' + ownSubmissionString + ' um ' + ownSubmissionString2 + ' abgegeben');

            } else {
                let timeleft = dueDate.getTime() - new Date().getTime();
                let timeleftDays = timeleft / 86400000;
                let timeleftHours = timeleftDays % 1;
                timeleftDays = Math.floor(timeleftDays);
                timeleftHours = timeleftHours * 24;
                timeleftHours = Math.floor(timeleftHours);

                $("#TaskStatus").css("color", "purple");
                $("#TaskStatus").text('Für die Abgabe sind noch ' + timeleftDays + ' Tage und ' + timeleftHours + ' Stunden Zeit');

                $("#addSubmissionFormSpan").show();
            }


        },
        error: function (error) {
            console.log(error);

        },
    });
}


function downloadTaskDescriptionFile(assignmentId) {
    notyf.success('Die Datei wird heruntergeladen!');
    window.location = rootPath + "/backend/requestHandler.php?method=downloadAssignmentFile&assignment_id=" + assignmentId;
}

function uploadSubmission() {

    let file = document.getElementById("submissionFileUploadInput").files[0];

    if (!file) {
        $("#noFileError").show();
        return;
    }

    let formData = new FormData();
    formData.append("attachment", file, file.name);
    formData.append("method", "addSubmission");
    formData.append("assignment_id", assignmentId);

    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        success: function (data) {
            notyf.success('Die Datei wurde abgegeben!');
            loadAssignmentDetails(assignmentId);
            $("#addSubmissionFormSpan").hide();
        },
        error: function (error) {
            console.log(error);
        },
        async: true,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    });
}