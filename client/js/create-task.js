var groupId = new URLSearchParams(window.location.search).get("id");
console.log("Loading Group with id: " + groupId);


function submitNewTask(){

    err = false;
    $("#noTitleError").hide();
    $("#noDescriptionError").hide();
    $("#noTimeError").hide();


    if(!$("#newAssignmentTitle").val()){
        $("#noTitleError").show();
        err = true;
    } 

    if(!$("#newAssignmentDescription").val()){
        $("#noDescriptionError").show();
        err = true;
    }

    if(!$('#newAssignmentTime').val()){
        $("#noTimeError").show();
        err = true;
    }

    if(err){
        return;
    }

    file = document.getElementById("newAssignmentFile").files[0];
    
    date = new Date($('#newAssignmentTime').val());
    dateStr =
    date.getFullYear() + "-" +
    ("00" + (date.getMonth() + 1)).slice(-2) + "-" +
    ("00" + date.getDate()).slice(-2) + "T" +
    ("00" + date.getHours()).slice(-2) + ":" +
    ("00" + date.getMinutes()).slice(-2) + ":" +
    ("00" + date.getSeconds()).slice(-2);

    formData = new FormData();
    formData.append("method", "createAssignment");
    formData.append("group_id", groupId);
    formData.append("due_time", dateStr);
    formData.append("title", $("#newAssignmentTitle").val());
    formData.append("text", $("#newAssignmentDescription").val());
    if(file){
        formData.append("attachment", file, file.name);
    }
    
    $.ajax({
      type: "POST",
      url: rootPath + "/backend/requestHandler.php",
      success: function (data) {
        loadPage('gruppe', groupId);
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