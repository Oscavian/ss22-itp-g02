loadMessages();

function loadMessages(){
    let counter = 0;
    // foreach
    $("#chatContent").append("<div class='card chatMsg msgOwn'>" +
    "<div class='card-text'>" +
    "eigene Chatnachricht ffffffffffffffffff fffffffffff fffffffffff ffffffffff ffffffffffffffffffffffff fffffffffff" +
    "</div></div>");
}




function sendMessage(){
    let message = $("#newChatMessage").val();
    console.log(message);
}