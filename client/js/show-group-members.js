var groupId = new URLSearchParams(window.location.search).get("id");
getGroupMembers();

function getGroupMembers() {
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
                    tablerow.append(`
                                    <td>
                                        <div style="display: flex; align-items: center;"><i class="bi bi-person-circle text-muted" style="font-size: 2rem; margin-bottom: -25px; margin-top: -21.5px;"></i>
                                            <span style="vertical-align: middle; margin: 1px 0 0 7px;">
                                                <span style="margin-left: 3px" id="userName">${g["username"]}</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td>${g["first_name"]}</td>
                                    <td>${g["last_name"]}</td>
                                    `);

                    if(isTeacher && g["user_type"] == "2"){
                        tablerow.append(`<td><span class="pw-reset-link">Passwort zurücksetzen<span></td>`);
                    }
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
                    $("#noStudentsMessage").show();
                }
                else{
                    if(isTeacher){
                        createStudentTableRow();
                    }
                    $("#student-member").show();
                }
                
                $("#addNewStudentsButton").click(() => {
                    console.log("test123");
                    loadPage('accountErstellen', groupId);
                })
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });
}

function createStudentTableRow(){

    var tablerow = $("<tr class='group-member' style='vertical-align: top;'></tr>");
    tablerow.append(`
                    <td  onclick="loadPage('accountErstellen', ${groupId}); "style="color: purple;">
                        <div style="display: flex; align-items: center;"><i class="bi bi-person-plus" style=" color: purple; margin-left: 3px; font-size: 2rem; margin-bottom: -25px; margin-top: -21.5px;"></i>
                            <span style="vertical-align: middle; margin: 1px 0 0 7px;">
                                <div style="margin-left: 3px">Schüler*in hinzufügen</div>
                            </span>
                        </div>
                    </td> 
                    <td></td>
                    <td></td>
                    <td></td>
                    `);

    $("#student-member-list").append(tablerow);
}