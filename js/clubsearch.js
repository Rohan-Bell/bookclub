
clubsearch.onInput() = function() {
    clubname = document.getElementById("clubsearch").value;
    formobject = new FormData(); //create a form object
    formobject.append("clubname", clubname); //email is a textinput tag value
    new_ajax_helper('/mobile/getclubs.php', handleresults, formobject); //send the formobject to the url, you can define a callback 
}

//This is an AJAX method for handling search results
function handleresults(results) //RECEIVES JSON RESPONSE
{
    console.log(results);
    // Get reference to the HTML table
    dropdown = document.getElementById('clubsearch');
    
    options = "";

    // Loop through the JSON array and insert each item
    results.forEach(function(item) {
        options = options + "<option value='" + String(item.clubid) + "'>" + String(item.clubname) + "</option>";
    });
    dropdown.innerHTML = options;

}

