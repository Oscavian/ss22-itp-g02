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
    $("#chatContent").append("<div class='card chatMsg msgOwn' id='msg" + 1 + "'>" +
    "<div class='card-text'>" +
    "eigene Chatnachricht ffffffffffffffffff fffffffffff fffffffffff ffffffffff ffffffffffffffffffffffff fffffffffff" +
    "</div></div>");
    if(isTeacher === true){
        $("#msg" + 1 + "").prepend("<button class='btn btn-sm btnDeleteMsg' onclick='deleteMessage()'><i class='bi bi-trash3'></i></button>");
    }
    
}

function sendMessage(){
    let message = $("#newChatMessage").val();
    console.log(message);
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "sendMessage", message: message},
        cache: false,
        dataType: "json",
        success: (response) => {}
    });
}

function deleteMessage(){
    let delMsgId = $(this).val();
    console.log(delMsgId);
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "deleteMessage", messageId: delMsgId},
        cache: false,
        dataType: "json",
        success: (response) => {}
    });
}