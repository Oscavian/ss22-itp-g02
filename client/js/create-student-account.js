var counter = 1;

defaultForms();

function defaultForms(){
     //per default 5 forms open
     while(counter <= 5){
        addStudentAccountForm();
    }

    $("#add-form").click(function () {
        addStudentAccountForm();
    }); 

    $("#submit-student-accounts").click(function () {
        createStudentAccounts();
    }); 
}

function getMyGroups(counter){
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getUserGroups"},
        cache: false,
        dataType: "json",
        success: (response) => {
            if (response["success"]){

                if (response["noGroups"]){
                    $("#class-selector" + counter).append("<option>Keine Klassen zur Auswahl</option>");
                } 
                else {
                    $.each(response["groups"], (i, g) => {
                        $("#class-selector" + counter).append("<option id='" + g["groupId"] + "'>" + g["groupName"] + "</option");
                    })
                }
            }
        },
        error: (error) => {
            console.log("AJAX Request Error: " + error);
        }
    });

}

function addStudentAccountForm(){
    var tablerow = $("<tr id ='" + counter +"' class = 'student-form' style='vertical-align: top;'></tr>");
    $(tablerow).append("<td class='vornameZeile'>\
                            <label for='firstname' class='col-sm-2 col-form-label col-form-label-lg'>Vorname</label>\
                            <input type='text' placeholder='Max' class='form-control form-control-lg bg-white' id='firstname" + counter + "' name='firstname'>\
                            <div id='firstname_error" + counter + "' style='color: red;'></div>\
                        </td>");

    $(tablerow).append("<td class='nachnameZeile'>\
                            <label for='lastname' margin-left: 5px' class='col-sm-2 col-form-label col-form-label-lg'>Nachname</label>\
                            <input type='text' placeholder='Mustermann' class='form-control form-control-lg bg-white' id='lastname" + counter + "' name='lastname' style='margin-left: 5px'>\
                            <div id='lastname_error" + counter + "' style='color: red;'></div>\
                        </td>");
    $(tablerow).append("<td class='klasseZeile'>\
                            <label for='class-selector' class='col-sm-2 col-form-label col-form-label-lg' margin-left: 10px;'>Klasse</label>\
                            <select id='class-selector" + counter + "' class='form-control form-control-lg bg-white' name='group' style=' margin-left: 10px;'>\
                            <option value='empty slot'></option>\
                            </select>\
                            <div id='class_error" + counter + "' style='color: red;'></div>\
                        </td>");
                        getMyGroups(counter);
    $(tablerow).append("<td>\
                            <label for='delete-form' margin-left: 15px;' class='col-sm-2 col-form-label col-form-label-lg'>Löschen</label><br>\
                            <button type='button' class='btn btn-lg btn-warning bg-warning' id='delete-form" + counter + "' style='height: 45px; width: 45px; margin-left: 15px;' onclick='deleteForm(" + counter + ")'> x </button>\
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
        //allOk = checkIfSelectedStudAcc(allOk, Id);
        if(allOk === true) {

            var student = new StudentInfo();
            student.first_name = $("#firstname" + Id).val();
            student.last_name = $("#lastname" + Id).val();
            //add group into it later on
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
        $("#post-response-stdacc").append("Es muss mindestens ein Formular korrekt ausgefüllt sein.<br>");
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
    if(!/^[A-Za-z\s]*$/.test(firstName)){
        allOk = false;
        $("#firstname_error"+id).append("Der Vorname darf nur aus Buchstaben bestehen");
    }
    if(!/^[A-Za-z\s]*$/.test(lastName)){
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
//is there for adding students to classes immediately
function checkIfSelectedStudAcc(allIsOk, id){
    var allOk = allIsOk;
    var opt = $("#class-selector"+id+" option").filter(':selected').text();

    if ( opt == "" ) {
        $("#class_error"+ id).append("Es muss eine Klasse ausgewählt werden");
        allOk = false;
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
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data:   {
                    method: "registerStudents",
                    students: JSON.stringify(studentInfoList)
                },
        cache: false,
        dataType: "json",
        success: function (response) {
            //$("#success").append(response);
            $("#post-response-stdacc").append("Die SchülerInnen-Accounts sind erfolgreich angelegt worden<br>");
            $.each(response, function(i, p) {
                $("#student-account-data").append("<li>Username: "+ p["username"] +", Passwort: "+ p["password"] +"</li>");
            });

            $('#student-account')[0].reset();
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
        }
    });
}