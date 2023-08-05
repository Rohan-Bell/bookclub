// This is a JavaScript file

console.log("MyScripts loaded!");



var SKIPLOGIN = true; //set this to true if you wish to skip logging on

//----GLOBAL VARIABLES-----------
var isDeviceReady = false;
var userid = 2;
var username = null;
var longitude = null;
var latitude = null;
var currentBookId = null;




//---DEVICE READY - (Mobile functionality is not available until device is ready) --------------
document.addEventListener("deviceready", onDeviceReady, false);

function onDeviceReady() {
  console.log("Device Ready");
  isDeviceReady = true;
  checkGPS(); //get GPS locations
}
// Load CSS FILE
// Function to create a <link> element for the CSS file
function loadCSSFile(filename) {
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = filename;
  document.head.appendChild(link);
}

// Call the function to load the style.css file
loadCSSFile('css/style.css');
//--LOGIN-------------------------------------------------------------
async function login() {
    if (SKIPLOGIN)
    {
 $.mobile.changePage( "#menupage", {transition: "slideup"});
    
    }
    else{
  // ...
  try {
    const formobject = new FormData();

    // Get the password and email from the form
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    // Append the encrypted password and email to the form object
    const encryptedEmail = encrypt(email, key);
    const encryptedPassword = encrypt(password, key);
    formobject.append("email", encryptedEmail);
    formobject.append("password", encryptedPassword);

    // Send the form object to the server
    new_ajax_helper(
      "https://book-club-s3161462201341.codeanyapp.com/mobile/mobilelogin.php",
      loginReceived,
      formobject,
      'POST'
    );

  } catch (error) {
    console.error(error);
  }
    }
}




//callback function from ajax
async function loginReceived(response) {
  console.log(response);
  if (response.result === "Success") {
    // Get the password and email from the form
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {

      $.mobile.changePage("#menupage", { transition: "slideup" });
    } catch (error) {
      console.error("Error:", error);
    }
  } else if (response.result === "Error") {
    console.log(response.message);
  }
}

//--GET GPS---------------------------------------------------------
function checkGPS()
{
  if (isDeviceReady)
  {
    navigator.geolocation.getCurrentPosition(onGPSSuccess, onError);
  }
}

//save GPS coordinates globally
function onGPSSuccess(position) {
  longitude = position.coords.longitude;
  latitude = position.coords.latitude;
  console.log("GPS coordinates received: Long: " + String(longitude) + " Lat: " + String(latitude)); 
};


//--GET BOOK INFORMATION--------------------------------------
function findbook()
{
  // Get the value of the search input
  var search = document.getElementById("booksearch").value;
      // Get the values entered by the user in the input fields
    var title = document.getElementById("booktitle").value;
    var author = document.getElementById("bookauthor").value;
    var subject = document.getElementById("booksubject").value;
    // Append the values to the formobject
  // Check if the search input is empty or not
  if (search.trim() !== "") {
      console.log("Performing a General Search");
    var formobject = new FormData(); //create a form object
    formobject.append("search", search.trim());
    console.log("Search:", search);
    new_ajax_helper("https://book-club-s3161462201341.codeanyapp.com/mobile/search.php", findBookReceived, formobject, 'POST');
    return; // Exit the function and do not proceed with the search
  }
  else if (title.trim() !== "" || author.trim() !=="" || subject.trim() !=="")
  {
      console.log("Performing an Advanced Search");

        //create a form object to send via ajax
        var formobject = new FormData(); //create a form object
            formobject.append("title", title.trim());
            formobject.append("author", author.trim());
            formobject.append("subject", subject.trim());
            console.log("Title:", title);
            console.log("Author:", author);
            console.log("subject:", subject);

        //send form object to server using POST request
        new_ajax_helper("https://book-club-s3161462201341.codeanyapp.com/mobile/findbook.php", findBookReceived, formobject, 'POST');
    
}
else{
    alert("Please Enter in a Valid Input");
}
}

function findBookReceived(response) {

  console.log(response.docs[0]); // Add this line to inspect the response
  var bookdata = document.getElementById("bookdata"); // Select the bookdata div

  bookdata.innerHTML = ""; // Clear previous data

  var docs = response.docs;

    if (!response || !response.docs || response.docs.length === 0) {
    bookdata.innerHTML = "No Books Found";
    return;
  }
  for (var i = 0; i < docs.length; i++) {
    let div = document.createElement('div'); // Create a HTML DIV
    div.className = "BookResult";

    let title = document.createElement('h3'); // Create a title html tag
    title.textContent = docs[i].title; // Set the tag to the title content

    let author = document.createElement('h4');
    author.textContent = docs[i].author_name && docs[i].author_name.length > 0 ? docs[i].author_name[0] : "Unknown Author"; // Check if author_name exists and is not empty


    let isbn = docs[i].isbn && docs[i].isbn.length > 0 ? docs[i].isbn[0] : "ISBN Not Available"; // Check if isbn exists and is not empty


    let img = document.createElement('img');
    img.src = "https://covers.openlibrary.org/b/isbn/" + isbn + "-M.jpg";

    let button = document.createElement('button');
    button.textContent = "Add to Book List";
    button.setAttribute('data-title', docs[i].title);
    button.setAttribute('data-author', author.textContent);
    button.setAttribute('data-isbn', isbn);
    // Add event listener to the button

    button.addEventListener('click', function() {
        // Get the book information from data attributes
        const title = this.getAttribute('data-title');
        const author = this.getAttribute('data-author');
        const isbn = parseInt(this.getAttribute('data-isbn'));

        // Output book information to the console
        console.log('Sending the following data to the server:');
        console.log('Title:', title);
        console.log('Author:', author);
        console.log('ISBN:', isbn);

        // Create a FormData object and append the book data to it
        var formObject = new FormData();
        formObject.append('title', title);
        formObject.append('author', author);
        formObject.append('isbn', isbn);

    // Create the API link with the ISBN
        var apiLink = '';
        if (!isNaN(isbn)) {
                apiLink = `https://openlibrary.org/isbn/${isbn}.json`;
                formObject.append('api_link', apiLink);
        }
        console.log('API Link with ISBN:', apiLink);

        // Send the form data to the server using the new_ajax_helper function
        new_ajax_helper(
            'https://book-club-s3161462201341.codeanyapp.com/mobile/addbooks.php',
            addbookcomplete,
            formObject,
            'POST'
    );
});



    // Append the elements to the bookdata div
    div.appendChild(title);
    div.appendChild(author);
    div.appendChild(img);
    div.appendChild(button);
    bookdata.appendChild(div);
  }
}
function addbookcomplete(response) {
  console.log(response);

  // Get the book information from the server response
  const bookAuthor = response.author;
  const bookISBN = response.isbn;
  const bookTitle = response.title;
  currentBookId = response.isbn;

  // Check if the popup with the specified ID already exists
  const existingPopup = $("#addBookPopup");
  if (existingPopup.length > 0) {
    // Remove the existing popup
    existingPopup.remove();
  }

  // Create the content for the popup
  const popupContent = `
    <div data-role="popup" id="addBookPopup" data-dismissible="false" style="max-width:400px; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
      <div data-role="header">
        <h1>Add Book</h1>
      </div>
      <div role="main" class="ui-content">
        <p>Would you like to add the book "${bookTitle}" by ${bookAuthor} to your reading list or club reading list?</p>
        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" onclick="addToReadingList()">My Reading List</a>
        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" onclick="addToClubReadingList()">Club Reading List</a>
      </div>
    </div>
  `;

  // Append the popup content to the page and open the popup
  $(popupContent).appendTo($.mobile.pageContainer);
  $("#addBookPopup").enhanceWithin().popup().popup("open");
}



function addToReadingList() {
  // Add code to handle adding the book to the user's reading list
  console.log("Added to My Reading List");
  console.log("UserID:", userid);
  console.log("BookID:", currentBookId);
  $("#addBookPopup").popup("close");
  // Use AJAX to send the bookid and userid to the server and execute the INSERT query
  var formObject = new FormData();
  formObject.append('bookid', currentBookId);
  formObject.append('userid', userid);
  $.ajax({
    url: 'https://book-club-s3161462201341.codeanyapp.com/mobile/addreadinglistbook.php',
    method: 'POST',
    data: formObject,
    processData: false,
    contentType: false,
    success: function(response) {
      // Handle the response from the server, e.g., show a success message
      console.log("Book added to reading list successfully");
    },
    error: function(error) {
      // Handle any errors that occurred during the AJAX request
    console.error("Book Already Exists in Database");
    }
  });
}


function addtoreadinglistrecieved(){
   

}

// Global variable to store the user's book clubs data
let userBookClubsData = [];

function addToClubReadingList() {
  // Add code to handle adding the book to the club's reading list
  console.log("Selected Club Reading List");
  console.log("BookID:", currentBookId);
  console.log("Userid:", userid);
  $("#addBookPopup").popup("close");
  searchmybookclubs();

  // Call searchmybookclubs() to fetch the user's book clubs data
}

function searchmybookclubs() {
  console.log("Searching for Users Book Clubs");
  console.log("Userid:", userid);
  // Use AJAX to send the userid to the server and execute the SELECT query
  var formObject = new FormData();
  formObject.append('userid', userid);

  new_ajax_helper(
    'https://book-club-s3161462201341.codeanyapp.com/mobile/searchmyclubs.php',
    searchmybookclubscomplete,
    formObject,
    'POST'
  );
}

function searchmybookclubscomplete(response) {
    userBookClubsData = response;
      console.log("User's Book Clubs Data:");
      console.log(userBookClubsData);
      showBookClubsPopup();
    
}

function showBookClubsPopup() {
  if ($("#bookClubsPopup").hasClass("ui-popup-active")) {
    $("#bookClubsPopup").popup("close");
  }
  console.log("Pop Up should Appear");
  let popupContent = `<div data-role="popup" id="bookClubsPopup" data-dismissible="false" style="max-width: 400px; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <div data-role="header">
                          <h1>My Book Clubs</h1>
                          <a href="#" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-notext" onclick="closePopup();"></a>
                        </div>
                        <div role="main" class="ui-content">`;

  if (userBookClubsData.length > 0) {
    userBookClubsData.forEach((bookClub) => {
      popupContent += `<p>${bookClub.clubname}</p>
                        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" onclick="selectBookClub(${bookClub.clubid})">Select this book club</a>`;
    });
  } else {
    popupContent += `<p>You Are Not an Admin to Any Book Clubs.</p>`;
  }

  popupContent += `</div></div>`;

  $(popupContent).appendTo($.mobile.pageContainer);
  $("#bookClubsPopup").enhanceWithin().popup().popup("open");
}

function closePopup() {
  $("#bookClubsPopup").popup("close");
}


function selectBookClub(clubId) {
  // Handle the selection of the book club with the given clubId
  console.log("Selected Book Club ID:", clubId);
  $("#bookClubsPopup").popup("close");
    var formObject = new FormData();
  formObject.append('userid', userid);
  formObject.append('clubid', clubId);
  formObject.append('bookid', currentBookId);
  console.log("Userid:", userid);
  console.log("Clubid:", clubId);
  console.log("bookid:", currentBookId);

  new_ajax_helper(
    'https://book-club-s3161462201341.codeanyapp.com/mobile/addbookclubreadinglist.php',
    selectBookClubComplete,
    formObject,
    'POST'
  );

}

function selectBookClubComplete(){
    console.log("Selected Club Reading List");
  console.log("BookID:", currentBookId);
  console.log("Userid:", userid);
  $("#addBookPopup").popup("close");
  showBookAddedMessage();

}    



function toggleAdvancedOptions() {
  var advancedOptionsDiv = document.getElementById("advancedOptions");
  var button = document.querySelector('button[onclick="toggleAdvancedOptions()"]');

  if (advancedOptionsDiv.style.display === "none") {
    advancedOptionsDiv.style.display = "block";
    button.textContent = "Hide Advanced Options";
  } else {
    advancedOptionsDiv.style.display = "none";
    button.textContent = "Advanced Options";
  }
}




function findmeetings()
{
        //create a form object to send via AJAX
        formobject = new FormData(); //create a form object
        formobject.append("userid", userid);

        //send form object to server using POST request
        new_ajax_helper('https://book-club-s3161462201341.codeanyapp.com/mobile/mobilelogin.php', recievemeetings, formobject, 'POST'); 
}
function recievemeetings(response)
{
    

}


function showBookAddedMessage() {
  // Move the tab down
  document.getElementById("tab").style.top = "60px"; // Adjust the value as needed

  // Show the "Book Added" message
  document.getElementById("bookAddedMessage").style.top = "40px"; // Adjust the value as needed

  // After a delay, reset the tab and hide the "Book Added" message
  setTimeout(function () {
    document.getElementById("tab").style.top = "-60px"; // Move the tab back off the top of the page
    document.getElementById("bookAddedMessage").style.top = "-40px"; // Move the message off the top of the page
  }, 2000); // 2000 milliseconds (2 seconds) - Adjust the delay as needed
}


//CLUBS FUNCTIONS:
 // Function to show the selected tab and load its data
function showTab(tabName) {
  // Hide all tabs and remove 'active' class from buttons
  $('#clubs-container > div').hide();
  $('#clubs-tabs button').removeClass('active');

  // Show the selected tab and add 'active' class to its button
  $('#' + tabName).show();
  $('#' + tabName + '-tab').addClass('active');

  // Load data for the selected tab
  switch (tabName) {
    case 'for-you':
      loadForYouClubs();
      break;
    case 'your-groups':
      loadYourGroups();
      break;
    case 'events':
      loadEvents();
      break;
    case 'find-group':
      loadFindGroup();
      break;
    default:
      break;
  }
}

// Sample data for Clubs (Replace this with real data fetched from the server)
const clubsData = [
  { name: 'Club 1', description: 'This is club 1 description.' },
  { name: 'Club 2', description: 'This is club 2 description.' },
  { name: 'Club 3', description: 'This is club 3 description.' }
  // Add more club objects as needed
];

// Function to load Clubs data into the 'For You' tab
function loadForYouClubs() {
  const $clubsContainer = $('#clubs-container');
  $clubsContainer.empty();
  clubsData.forEach((club, index) => {
    $clubsContainer.append(`
      <div class="club-card">
        <h3>${club.name}</h3>
        <p>${club.description}</p>
      </div>
    `);
  });
}

// Function to find all groups where the user is the admin
function findGroupsManagedByUser() {
    console.log(userid);
  return new Promise((resolve, reject) => {
    const formObject = new FormData();
    formObject.append('userid', userid);
  
    new_ajax_helper(
      'https://book-club-s3161462201341.codeanyapp.com/mobile/searchmyclubs.php',
      function (response) {
        // Assuming the response is an array of groups managed by the user
        const groupsManagedData = response; // If the response is JSON
        resolve(groupsManagedData);
      },
      formObject,
      'POST'
    );
  });
}

// Function to find all groups where the user is a member
function findGroupsJoinedByUser() {
  return new Promise((resolve, reject) => {
    const formObject = new FormData();
    formObject.append('userid', userid);

    new_ajax_helper(
      'https://book-club-s3161462201341.codeanyapp.com/mobile/searchmemberclubs.php',
      function (response) {
        const groupsJoinedData = response;
        resolve(groupsJoinedData);
      },
      formObject,
      'POST'
    );
  });
}
// Function to load Clubs data into the 'Your Groups' tab
async function loadYourGroups() {
  const $clubsContainer = $('#clubs-container');
  $clubsContainer.empty();

  try {
    // Use Promises.all to fetch both groupsManaged and groupsJoined simultaneously
    const [groupsManaged, groupsJoined] = await Promise.all([
      findGroupsManagedByUser(),
      findGroupsJoinedByUser(),
    ]);
    console.log(groupsManaged);
    console.log(groupsJoined);

    if (groupsManaged.length > 0) {
      $clubsContainer.append('<h2>Groups You Manage:</h2>');
      groupsManaged.forEach((group) => {
        $clubsContainer.append(`
          <div class="club-card">
            <h3>${group.clubname}</h3>
            <p>Suburb: ${group.suburb}, State: ${group.state}</p>
          </div>
        `);
      });
    } else {
      $clubsContainer.append('<h2>Groups You Manage:</h2>');
      $clubsContainer.append('<button onclick="createGroup()">Create Group</button>');
    }

    if (groupsJoined.length > 0) {
      $clubsContainer.append('<h2>Groups You\'ve Joined:</h2>');
      $clubsContainer.append(`
        <input type="text" id="groupSearch" placeholder="Search for a group..." oninput="searchGroups()">
      `);
      groupsJoined.forEach((group) => {
        $clubsContainer.append(`
          <div class="club-card">
            <h3>${group.name}</h3>
            <p>${group.description}</p>
          </div>
        `);
      });
    } else {
      $clubsContainer.append('<h2>Groups You\'ve Joined:</h2>');
      $clubsContainer.append('<button onclick="createGroup()">Join a Group</button>');
    }

  } catch (error) {
    // Handle any errors that may occur during the AJAX request or data processing
    console.error('Error loading your groups:', error);
  }
}



// Function to search for groups
function searchGroups() {
  const searchQuery = $('#groupSearch').val();
  // Implement the search functionality based on the searchQuery
  // ...
}

// Function to create a new group




// Function to search for groups
function searchGroups() {
  const searchQuery = $('#groupSearch').val();
  // Implement the search functionality based on the searchQuery
  // ...
}

// Function to create a new group
function createGroup() {
  // Implement the create group functionality here
  // ...
}

// Other functions as before...

// Load initial data when the page loads
$(document).on('pagecreate', '#clubspage', function() {
  loadForYouClubs();
});

async function loadEvents() {
  const $clubsContainer = $('#clubs-container');
  $clubsContainer.empty();

  // Set Mapbox access token here
  mapboxgl.accessToken = 'pk.eyJ1IjoiczMxNjE0NjIiLCJhIjoiY2xrdGxlOTdmMDBhbjNlcDZyd3dkbXkzciJ9.mhgMt_FK4lwZ4IZxumflKw';

  try {
    const eventsData = await findEventsByUser(); // Assuming findEventsByUser() fetches events data for the user
    console.log(eventsData);

    if (eventsData.length > 0) {
      console.log("Pop-up should Appear");

      // Create a hashmap to store events by meeting ID
      const eventsMap = {};

      eventsData.forEach((eventArray) => {
        eventArray.forEach((event) => {
          console.log(event);

          // Store event in the hashmap using meeting ID as key
          eventsMap[event.meetingid] = event;

          $clubsContainer.append(`
            <div class="event-card" style="background-color: #6c0b1e; margin-bottom: 10px; padding: 10px;">
              <h3>${event.bookname}</h3>
              <p>Location: ${event.meetinglocation}</p>
              <p>Meeting Time: ${formatMeetingTime(event.meetingtime)}</p>
              <p>Book Name: ${event.bookname}</p>
              <img src="https://covers.openlibrary.org/b/isbn/${event.bookid}-M.jpg" alt="${event.bookname}" style="max-width: 200px; margin-bottom: 10px;">
              <div id="map-${event.meetingid}" style="height: 200px; width: 100%;"></div>
            </div>
          `);

        });
      });

      // After all the event cards have been added to the DOM, create Mapbox maps for each event using the hashmap
      for (const meetingId in eventsMap) {
        const event = eventsMap[meetingId];
        createMapForEvent(event);
      }

    } else {
      $clubsContainer.append('<h2 style="color: #f8f8f8;">No Events Found</h2>');
    }
  } catch (error) {
    // Handle any errors that may occur during the AJAX request or data processing
    console.error('Error loading events:', error);
  }
}


function createMapForEvent(event) {
  if (!event.meetingid) {
    console.error(`Meeting ID missing for event: ${event}`);
    return;
  }

  const mapContainer = `map-${event.meetingid}`;
  const map = new mapboxgl.Map({
    container: mapContainer,
    style: 'mapbox://styles/s3161462/clktnjxeu00cv01q24ccdfd8v',
    center: [event.longitude, event.latitude],
    zoom: 12 // Set the initial zoom level
  });

  // Add a marker for the meeting location
  new mapboxgl.Marker()
    .setLngLat([event.longitude, event.latitude])
    .addTo(map);

  console.log(`Map created for meeting ID: ${event.meetingid}`);
  console.log(`Map container ID: ${mapContainer}`);
}
// Function to find all groups where the user is a member
function findEventsByUser() {
  return new Promise((resolve, reject) => {
    const formObject = new FormData();
    formObject.append('userid', userid);

    new_ajax_helper(
      'https://book-club-s3161462201341.codeanyapp.com/mobile/searcheventsbyuser.php',
      function (response) {
        const eventsData = response;
        resolve(eventsData);
      },
      formObject,
      'POST'
    );
  });
}


  function formatMeetingTime(meetingtime) {
    const options = {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
    };
    return new Date(meetingtime).toLocaleString(undefined, options);
  }


// Function to load Clubs data into the 'Find Group' tab
function loadFindGroup() {
  // You can implement this function similarly to loadForYouClubs() if needed
}

// Load initial data when the page loads
$(document).on('pagecreate', '#clubspage', function() {
  loadForYouClubs();
});


function encrypt(message, key) {
  var encrypted = CryptoJS.AES.encrypt(message, key, { iv: key });
  return encrypted.toString();
}

// Change the decryption algorithm to AES-256-GCM
function decrypt(encryptedMessage, key) {
  var decrypted = CryptoJS.AES.decrypt(encryptedMessage, key, { iv: key });
  return decrypted.toString(CryptoJS.enc.Utf8);
}

const key = "MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAOt+ZYHYpjb3tepG\
R9I0oew3QjWd+Ev6hNX36XKtFH2iAgXoPIURKK4bjKh8NpcD+U6GYyfNQy+QjP4w\
IjiQ7LTHbRTK3O32+Dz0XyFOTgdh43YVlGpc4CD44+FMMnT0rEb5iqYP8tXl3cui\
fMpgiDqPJbR1cokhBHP7jqfxP3lzAgMBAAECgYAzD4ru5ozTqk41Z9u2xla9oxWm\
DoSB5OXchw6FIOYqf3A027AToi7R4YNUHOqxP9lUn2rsfGMu8wa/Lqyc2z+XYIZZ\
R+JJrkY8lVxKeYXrJKnHBWVaKQip1kuJIIQ0TKkL0x7SmMxX+WmVrBmEh2jsDRBu\
QZzqOaWLQ80HnHFAUQJBAP2HGZjk9jfP5cHUbafJctO62Uj4ueXKuxEHQ7vCmnju\
kq035EJx7M1RpSGGjYQE80NRbdmI9M4rhtE3V8OhUp8CQQDtykbkXZ5zrJhGz710\
SJzeM+6RgrpUqL+/6ixtEQqRshf077/BfQFZYAdqKzpbyLhKIIeSc4JDEek2uvxF\
htytAkBAsj0h+yupym/DKsZgztNynHfeWzp1HWrnSYdLXrm0qozbjyu8mP3o1zDg\
gUmTqv+46gqyPHTcN9dLhGftEDnZAkEAmUz+FT4R6EtOROCrKGujzlE2rdLU472D\
GWwURiBlavahIUTroAdCNmeFgDnsPr4RqaB9JrqXMTw72RAFkaFXZQJAQWhQ8OFT\
e88z3NL2+Lgy9l2yDea3T9WOOrNrZKLUqFetsAJ57MTonOgyVTzFmOAdX0SZIdwV\
2qw/cqASgfRRXQ==";

