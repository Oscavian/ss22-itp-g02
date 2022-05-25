var groupId = new URLSearchParams(window.location.search).get("id");
getUserGroups();

function getUserGroups() {
    $("#teacher-member-list").empty();
    $("#student-member-list").empty();
    $("#noStudents").empty();

    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getGroupMembers", group_id: groupId},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){
                $.each(response["groupMembers"], (i, g) => {
                    var tablerow = $("<tr class='group-member' style='vertical-align: top;'></tr>");
                    tablerow.append("<td>"+ g["user_id"] +"</td>\
                                     <td>"+ g["first_name"] +"</td>\
                                     <td>"+ g["last_name"] +"</td>\
                                     <td>"+ g["username"] +"</td>");
                    if(g["user_type"] == "1"){
                        $("#teacher-member-list").append(tablerow);
                    }
                    else if(g["user_type"] == "2"){
                        $("#student-member-list").append(tablerow);
                    } 
                }) 
                $("#teacher-member").show();
                var studentRows = $('#student-member tr').length - 1;//tr mit beschreibungen wird auch gezählt
                if(studentRows === 0){
                    $("#noStudents").append("In dieser Gruppe sind keine Schüler*innen");
                }
                else{
                    $("#student-member").show();
                }
                
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });
}