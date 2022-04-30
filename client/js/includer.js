$(document).ready(function() {
    //path to html-file that should be included
    fetch("../basic/navbar.html")
    .then(response => {
        return response.text()
    })
    .then(data => {
        //query-selector in DOM fetched file-content should be placed in
        document.querySelector("navbar").innerHTML = data;
    }); 
    
    fetch("../basic/footer.html")
    .then(response => {
        return response.text()
    })
    .then(data => {
        document.querySelector("footer").innerHTML = data;
    }); 
    //works with any kind of HTML-content (also with bootstrap included); 
    //query selector used must be in DOM
});
    