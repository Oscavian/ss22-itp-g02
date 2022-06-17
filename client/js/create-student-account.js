var groupId = new URLSearchParams(window.location.search).get("id");

var counter = 1;

defaultForms();

function defaultForms(){

    $("#form-list").empty();
    addStudentAccountForm();

    $("#add-form").off();
    $("#add-form").click(function () {
        addStudentAccountForm();
    }); 

    $("#submit-student-accounts").off();
    $("#submit-student-accounts").click(function () {
        createStudentAccounts();
    }); 
}

function addStudentAccountForm(){
    var tablerow = $("<tr id ='" + counter +"' class = 'student-form' style='vertical-align: top;'></tr>");
    $(tablerow).append("<td class='vornameZeile'>\
                            <label for='firstname' style='font-size: 20px'>Vorname</label>\
                            <input style='padding-left: 10px; padding-right: 5px;' type='text' placeholder='Max' class='form-control form-control-lg' id='firstname" + counter + "' name='firstname'>\
                            <div id='firstname_error" + counter + "' style='color: red;'></div>\
                        </td>");

    $(tablerow).append("<td style='padding-left: 10px; padding-right: 10px' class='nachnameZeile'>\
                            <label for='lastname' style='font-size: 20px'>Nachname</label>\
                            <input style='padding-left: 10px; padding-right: 5px;' type='text' placeholder='Mustermann' class='form-control form-control-lg' id='lastname" + counter + "' name='lastname'>\
                            <div id='lastname_error" + counter + "' style='color: red;'></div>\
                        </td>");
    $(tablerow).append("<td style='width: 1%; white-space: nowrap;'>\
                            <label for='delete-form' style='font-size: 20px; visibility: hidden;' class=''>x</label><br>\
                            <div style='border: 0; display: flex; align-items: center; justify-content: center; height: 48px; width: 48px; background-color: #eee;' id='delete-form" + counter + "' onclick='deleteForm(" + counter + ")' class='btn btn-light'><i class='bi bi-x-lg' style='margin-top: 2px; color: purple; font-size: 20px'></i></div>\
                            <div></div>\
                        </td>");
    $("#form-list").append(tablerow);
    
    counter++;
}

function deleteForm(id){
    $("#"+id).remove();
}

function createStudentAccounts(){
    var studentInfoList = [];
    class StudentInfo {
        first_name;
        last_name;
        //groupId;
    }
    emptyStudAccErrors();
    var allOk = true;
    let counter = 0;
    $("tr.student-form").each(function() {
        counter++;
        var Id = $(this).attr("id");
        allOk = checkIfEmptyStudAcc(allOk, Id);
        allOk = checkIfAlphabetStudAcc(allOk, Id);
        allOk = checkLengthStudAcc(allOk, Id);
        if(allOk === true) {

            var student = new StudentInfo();
            student.first_name = $("#firstname" + Id).val();
            student.last_name = $("#lastname" + Id).val();
            studentInfoList.push(student);
            //$("#checknumrows").append("row"+Id+" is ok, ");
        }
        
    });
    if(allOk === true && counter > 0) {
        //here submitting of array of first and last names
        submitStudentAccInput(studentInfoList);
        studentInfoList = [];
    }
    else if(counter === 0){
        $("#post-response-stdacc").append("Es muss mindestens ein Formular korrekt ausgefüllt sein");
    }
}

function checkIfEmptyStudAcc(allIsOk, id) {
    var allOk = allIsOk;
    if($("#firstname" + id).val() === ""){
        allOk = false;
        $("#firstname_error" + id).append("Bitte geben Sie einen Vornamen ein");
    }
    if($("#lastname" + id).val() === ""){
        allOk = false;
        $("#lastname_error" + id).append("Bitte geben Sie einen Nachnamen ein");
    }
    return allOk;
}

function checkIfAlphabetStudAcc(allIsOk, id){
    //checks if first and last name consist of letters only
    var allOk = allIsOk;
    var firstName = $("#firstname"+id).val();
    var lastName = $("#lastname"+id).val();
    if(!/^[A-Za-zäöüÄÖÜß\s]*$/.test(firstName)){
        allOk = false;
        $("#firstname_error"+id).append("Der Vorname darf nur aus Buchstaben bestehen");
    }
    if(!/^[A-Za-zäöüÄÖÜß\s]*$/.test(lastName)){
        allOk = false;
        $("#lastname_error"+id).append("Der Nachname darf nur aus Buchstaben bestehen");
    }
    return allOk;
}
function checkLengthStudAcc(allIsOk, id){
    var allOk = allIsOk;
    if($("#firstname"+id).val().length > 50){
        allOk = false;
        $("#firstname_error"+id).append("Der Vorname muss weniger als 50 Zeichen lang sein");
    }
    if($("#lastname"+id).val().length > 50){
        allOk = false;
        $("#lastname_error"+id).append("Der Nachname muss weniger als 50 Zeichen lang sein");
    }
    return allOk;
}

function emptyStudAccErrors(){
    $("div[id^='firstname_error']").empty();
    $("div[id^='lastname_error']").empty();
    $("#post-response-stdacc").empty();
    $("#student-account-data").empty();

}

function submitStudentAccInput(studentInfoList){
    $.ajax({
        type: "POST",
        url: rootPath + "/backend/requestHandler.php",
        data:   {
                    method: "registerStudents",
                    students: JSON.stringify(studentInfoList),
                    group_id: groupId
                },
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
            $("#post-response-stdacc").append("Die SchülerInnen-Accounts sind erfolgreich angelegt worden<br>");
            $("#newStudentAccountListBody").empty();
            $("#createNewStudentsFormDiv").hide();
            $("#newStudentAccountList").show();

            $.each(response, function(i, p) {


                var tablerow = $("<tr class='newStudentAccount' style='vertical-align: top;'></tr>");
                tablerow.append(`
                                <td>
                                    <div style="display: flex; align-items: center;"><i class="bi bi-person-circle text-muted" style="font-size: 2rem; margin-bottom: -25px; margin-top: -21.5px;"></i>
                                        <span style="vertical-align: middle; margin: 1px 0 0 7px;">
                                            <span style="margin-left: 3px" id="userName">${p["username"]}</span>
                                        </span>
                                    </div>
                                </td>
                                <td>${p["first_name"]}</td>
                                <td>${p["last_name"]}</td>
                                <td>${p["password"]}</td>
                                `);

                $("#newStudentAccountListBody").append(tablerow);


            });

            $('#student-account')[0].reset();
        },
        error: function(error){
            console.log(error);
        }
    });
}