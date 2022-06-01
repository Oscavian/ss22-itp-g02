let counter = 0; // unnötig wenn ids von der DB genommen werden
loadMessages();
function loadMessages(){
    /*$.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getMessages", chatId_groupId: hi},
        cache: false,
        dataType: "json",
        success: (response) => {
            let counter = 0; wenn nur eine gewisse Anzahl Nachrichten geladen werden soll
            $.each(response =>{
                while(counter < 25){
                    Nachricht einfügen
                    counter++;
                }
            });
        }
    });*/
    ///////////////////// folgende Zahlen sind nur Platzhalter für die messageId aus der DB, Vorname + Nachname + Uhrzeit ebenfalls
    while(counter < 5){
        $("#textsField").append("<div class='card chatMsg msgOwn' id='msg" + counter + "'>" +
        "<div class='senderInfo'>Vorname Nachname - Uhrzeit</div>" +
        "<div class='card-text'>" +
        "eigene Chatnachricht ffffffffffffffffff fffffffffff fffffffffff ffffffffff ffffffffffffffffffffffff fffffffffff" +
        "</div></div>");
        if(isTeacher === true){
            $("#msg" + counter + "").append("<button class='btn btn-sm py-0 px-1 btnDeleteMsg' onclick='deleteMessage(" + counter + ")'><i class='bi bi-trash3'></i></button>");
        }
        counter++;
    }
    
    $('#chatContent').animate({scrollTop: document.body.scrollHeight},"fast");
}

function sendMessage(){
    let message = $("#newChatMessage").val();
    $("#newChatMessage").val("");
    console.log(message);
    /*$.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "sendMessage", message: message},
        cache: false,
        dataType: "json",
        success: (response) => {}
    });*/
    $("#textsField").append("<div class='card chatMsg msgOwn' id='msg" + counter + "'>" +
    "<div class='senderInfo'>Vorname Nachname - Uhrzeit</div>" +
    "<div class='card-text'>" + message +
    "</div></div>");
    if(isTeacher === true){
        $("#msg" + counter + "").append("<button class='btn btn-sm py-0 px-1 btnDeleteMsg' onclick='deleteMessage(" + counter + ")'><i class='bi bi-trash3'></i></button>");
    }
    counter++;
    $('#chatContent').animate({scrollTop: document.body.scrollHeight},"fast");
}

function deleteMessage(msgId){
    console.log(msgId);
    /*$.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "deleteMessage", messageId: msgId},
        cache: false,
        dataType: "json",
        success: (response) => {}
    });*/
}