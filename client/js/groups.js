$("#oneGroupTable").hide();
$("#backToGroupList").hide();
getUserGroups();

function getUserGroups() {
    $("#oneGroupTable").fadeOut(200);
    $("#backToGroupList").fadeOut(200);
    let tbody = $(".showAllGroupsTable");
    tbody.empty();

    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            console.log(response);
            if (response["success"]){
                if (response["noGroups"]){
                    tbody.append("" +
                        "<tr>" +
                        "   <td>XXX</td>" +
                        "   <td>Du gehörst noch zu keiner Gruppe.</td>" +
                        "   <td>XXX</td>" +
                        "</tr>");
                } else {
                    $.each(response["groups"], (i, g) => {
                        tbody.append("" +
                            "<tr rowId='" + g["groupId"] + "'>" +
                            "   <td>" + g["groupId"] + "</td>" +
                            "   <td><a onclick='loadPageGroupDetails(" + g["groupId"] + ")'><u>" + g["groupName"] + "</u></a></td>" +
                            "   <td><a onclick=''><u>" + g["groupChatId"] + "</u></a></td>" +
                            "</tr>");
                    })
                }
            }
            $("#allGroupsTable").delay(300).fadeIn(200);
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });

}

// function getGroupDetails(){
//     $("#allGroupsTable").fadeOut(200);
//     let tbody = $(".showStudentsOfGroup");
//     tbody.empty();

//     $.ajax({
//         type: "GET",
//         url: "/ss22-itp-g02/backend/requestHandler.php",
//         data: {method: "getStudentsOfGroup"},
//         cache: false,
//         dataType: "json",
//         success: (response) => {
//             if (response["success"]){
//                 if (response["noGroups"]){
//                     tbody.append("" +
//                         "<tr>" +
//                         "   <td>es sind noch keine Einträge hier vorhanden</td>" +
//                         "   <td>XXX</td>" +
//                         "   <td>XXX</td>" +
//                         "   <td>XXX</td>" +
//                         "</tr>");
//                 }
//                 else {
//                     console.log(response);
//                     /*$.each(response => {

//                     });
//                     $.each(response["groups"], (i, g) => {
//                         tbody.append("" +
//                             "<tr rowId='" + g["groupId"] + "'>" +
//                             "   <td>" + g["groupId"] + "</td>" +
//                             "   <td><a onclick='getGroupDetails()'><u>" + g["groupName"] + "</u></a></td>" +
//                             "</tr>");
//                     });*/
//                 }
//             }
//         },
//         error: (error) => {
//             tbody.append("" +
//                         "<tr>" +
//                         "   <td style='color: red'><b>ein Fehler ist aufgetreten</b></td>" +
//                         "   <td>XXX</td>" +
//                         "   <td>XXX</td>" +
//                         "   <td>XXX</td>" +
//                         "</tr>");
//             console.log("AJAX Request Error: " + error);
//         }
//     });
//     $("#oneGroupTable").delay(300).fadeIn(200);
//     $("#backToGroupList").delay(300).fadeIn(200);
// }