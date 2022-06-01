loadMessages();

function loadMessages(){
    /*$.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getMessages", chatId_groupId: hi},
        cache: false,
        dataType: "json",
        success: (response) => {
            let counter = 0;
            $.each(response =>{
                counter++;
                if(counter >= 25){
                    break;
                }
            });
        }
    });*/
    ///////////////////// folgende Zahlen sind nur Platzhalter für die messageId aus der DB
    $("#textsField").append("<div class='card chatMsg msgOwn' id='msg" + 1 + "'>" +
    "<div class='senderInfo'>Vorname Nachname - Uhrzeit</div>" +
    "<div class='card-text'>" +
    "eigene Chatnachricht ffffffffffffffffff fffffffffff fffffffffff ffffffffff ffffffffffffffffffffffff fffffffffff" +
    "</div></div>");
    if(isTeacher === true){
        $("#msg" + 1 + "").append("<button class='btn btn-sm py-0 btnDeleteMsg' onclick='deleteMessage(" + 1 + ")'><i class='bi bi-trash3'></i></button>");
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