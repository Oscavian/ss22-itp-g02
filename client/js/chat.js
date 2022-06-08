var groupId = new URLSearchParams(window.location.search).get("id");
loadfirstMessages();

var messageLoadOffset = 0;
var currentDate = new Date();

var isTeacher;

async function loadfirstMessages(){
    isTeacher = await checkIsTeacher();
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getMessages", group_id: groupId, offset: 0},
        cache: false,
        dataType: "json",
        success: (response) => {

            if(!response.length){ //prevents scroll eventlistener from being set when there are no more messages to load
                return;
            }

            currentDate = new Date(response[0]["time"]);
            $.each(response, (index, message) => {

                messageDate = new Date(message["time"])

                if(messageDate.getDate() != currentDate.getDate() || ((messageDate - currentDate) / 86400000) > 2){
                    insertDateTag(currentDate);
                    currentDate = messageDate;
                }
                
                insertMessage(message);
            });

            chatContent = $("#chatContent")
            chatContent.animate({ scrollTop: chatContent[0].scrollHeight }, 0);
        
            chatContent.off("scroll");
            chatContent.on("scroll", function() {
                var pos = chatContent.scrollTop();
                if (pos < 200) {
                    chatContent.off("scroll");
                    loadMoreMessages();
                }
            });

        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
            alert("Error loading messages!");
        }
    });
}

function loadMoreMessages(){

    messageLoadOffset++;

    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "getMessages", group_id: groupId, offset: messageLoadOffset},
        cache: false,
        dataType: "json",
        success: (response) => {

            if(!response.length){ //prevents scroll eventlistener from being set when there are no more messages to load
                insertDateTag(currentDate);
                return;
            }

            $.each(response, (index, message) => {

                messageDate = new Date(message["time"])

                if(messageDate.getDate() != currentDate.getDate() || ((messageDate - currentDate) / 86400000) > 2){
                    insertDateTag(currentDate);
                    currentDate = messageDate;
                }
                
                insertMessage(message);
            });
        
            chatContent.off("scroll");
            chatContent.on("scroll", function() {
                var pos = chatContent.scrollTop();
                if (pos < 200) {
                    chatContent.off("scroll");
                    loadMoreMessages();
                }
            });
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
            alert("Error loading messages!");
        }
    });
}

function insertDateTag(date) {
    $("#textsField").prepend(`
    <div class="chatDate">
        <span>${String(date.getDate()).padStart(2, "0")}.${String(date.getMonth() + 1).padStart(2, "0")}</span>
    </div>`);
}

function insertMessage(message) {

    insertMessageDate = new Date(message["time"])
    insertMessageDateHours = new String(insertMessageDate.getHours()).padStart(2, "0");
    insertMessageDateMinutes = new String(insertMessageDate.getMinutes()).padStart(2, "0");
    insertMessageTimeString = insertMessageDateHours + ":" + insertMessageDateMinutes;

    $("#textsField").prepend(`
    <div id='message${message["pk_message_id"]}' class="${message["isOwnMessage"] ? "ownMessageFlex" : "otherMessageFlex"}">
        <div class='card chatMsg ${message["isOwnMessage"] ? "msgOwn" : "msgOther"}'>
            <div class='senderInfo'>${!message["isOwnMessage"] ? message["first_name"] + " " + message["last_name"] : "Du"} - ${insertMessageTimeString}</div>
            <div class='card-text'>${message["text"]}</div>
        </div>
        ${isTeacher ? "<button class='btn btn-sm py-0 px-1 btnDeleteMsg' onclick='deleteMessage(" + message["pk_message_id"] + ")'><i class='bi bi-trash3'></i></button>" : ''}
    </div>`);
}

function insertNewSentMessage(message) {
    
    insertMessageDate = new Date(message["time"])
    insertMessageDateHours = new String(insertMessageDate.getHours()).padStart(2, "0");
    insertMessageDateMinutes = new String(insertMessageDate.getMinutes()).padStart(2, "0");
    insertMessageTimeString = insertMessageDateHours + ":" + insertMessageDateMinutes;

    $("#textsField").append(`
    <div id='message${message["pk_message_id"]}' class="ownMessageFlex">
        <div class='card chatMsg msgOwn'>
            <div class='senderInfo'>Du - ${insertMessageTimeString}</div>
            <div class='card-text'>${message["text"]}</div>
        </div>
        ${isTeacher ? "<button class='btn btn-sm py-0 px-1 btnDeleteMsg' onclick='deleteMessage(" + message["pk_message_id"] + ")'><i class='bi bi-trash3'></i></button>" : ''}
    </div>`);
}

function sendMessage(e){
    e.preventDefault();
    let messageText = $("#newChatMessage").val();

    if(messageText == ""){
        return;
    }

    $("#newChatMessage").val("");

    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "sendMessage", text: messageText, group_id: groupId},
        cache: false,
        dataType: "json",
        success: (response) => {
            if(response["success"]){
                message = {
                    "time": new Date().getTime(),
                    "text" : messageText,
                    "pk_message_id": response["message_id"]
                };
                insertNewSentMessage(message);
            }
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
            alert("Error sending message!");
        }
    });

    chatContent = $("#chatContent")
    chatContent.animate({ scrollTop: chatContent[0].scrollHeight }, "fast");
}

function deleteMessage(msgId){
    $.ajax({
        type: "POST",
        url: "/ss22-itp-g02/backend/requestHandler.php",
        data: {method: "deleteMessage", message_id: msgId},
        cache: false,
        dataType: "json",
        success: (response) => {
            if(response["success"]){
                $("#message" + msgId).remove();
            }
        },
        error: function(error){
            console.log("AJAX-Request error: " + error);
            alert("Error deleting Message!");
        }
    });
}