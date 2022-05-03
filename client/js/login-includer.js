$(document).ready(function() {
    //path to html-file that should be included
    fetch("/ss22-itp-g02/client/html-includes/login-modal.html")
    .then(response => {
        return response.text()
    })
    .then(data => {
        //query-selector in DOM fetched file-content should be placed in
        document.querySelector("login-modal").innerHTML = data;
    });

});

function waitForModal() {
    if ($( "#loginModal" ).length) {
        $("#showPw").change(function(){
            if($(this).is(':checked')){
             $("#password").attr("type","text");
             $("#showPwText").text("Hide");
            }else{
             // Changing type attribute
             $("#password").attr("type","password");
            
             // Change the Text
             $("#showPwText").text("Show");
            }  
        });
    } 
    else {
      setTimeout(function() {
        waitForModal();
      }, 100);
    }
  };