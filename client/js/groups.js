getUserGroups();

function getUserGroups() {

    let tbody = $("#group-list-table-body");
    tbody.empty();

    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){
                if (response["noGroups"]){
                    tbody.append("" +
                        "<tr>" +
                        "   <td>XXX</td>" +
                        "   <td>Du gehörst noch zu keiner Gruppe.</td>" +
                        "</tr>");
                } else {
                    $.each(response["groups"], (i, g) => {
                        tbody.append("" +
                            "<tr>" +
                            "   <td>" + g["groupId"] + "</td>" +
                            "   <td>" + g["groupName"] + "</td>" +
                            "</tr>");
                    })
                }
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });

}