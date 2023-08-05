<?php
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (for testing purposes)
header("Access-Control-Allow-Methods: POST"); // Allow only POST requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow only Content-Type header



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight request
    http_response_code(200);
    error_log("gothere\n", 3, "../error.log");
    exit;
}

require_once 'meekrodb.2.3.class.php';

// Connect info
DB::$user = 'root';
DB::$password = '';
DB::$dbName = 'bookreview';


//  $_POST['title'] = "Sherlock Holmes";
//  $_POST['author'] = "Arthur Conan Doyle";
//  $_POST['isbn'] = 90909999999;


// Check if the request is a POST request
if (isset($_POST['title'])) {

    // Get the book information from the form data
    // $title = trim(stripslashes(htmlspecialchars($_POST['title'])));
    // $author = trim(stripslashes(htmlspecialchars($_POST['author'])));
    // $isbn = trim(stripslashes(htmlspecialchars($_POST['isbn'])));
    // $apilink = trim(stripslashes(htmlspecialchars($_POST['api_link'])));

    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = intval($_POST['isbn']);
    $apilink = $_POST['api_link'];

    error_log($title.$author.$isbn."\n", 3, "../error.log");
    // Validate the received data (you can add more validation as per your requirements)
    if (empty($title) || empty($author) || empty($isbn)) {
        // Return an error response
        http_response_code(400);
        echo json_encode(array("error" => "Incomplete book data."));
        exit;
    }

    $searchr = DB::queryFirstRow("SELECT * FROM books WHERE bookid = %s", $isbn);
    if (!$searchr) {

         // Prepare the SQL INSERT query with placeholders
    $query = "INSERT INTO books (bookid, bookname, author, apilink) 
    VALUES (%s, %s, %s, %s)";

    // Format the query with actual values using MeekroDB's query() method
    $result = DB::query($query, $isbn, $title, $author,$apilink);

    if ($result) {
        // Book added successfully
        $response = array(
        "success" => true,
        "message" => "Book added successfully.",
        "isbn" => $isbn,
        "title" => $title,
        "author" => $author
        );
        http_response_code(200);
    } else {
        // Failed to add book
        $response = array("success" => false, "message" => "Failed to add book.");
        http_response_code(500);
    }

echo json_encode($response);

    } else{
        // Book already exists, so we can handle this case as needed
        $response = array("success" => false, "message" => "Book already exists in database.");
        //Will update the database to new information presented and store the old information into the backup books table
        $existingIsbn = $searchr['bookid'];
        $existingbookname = $searchr['bookname'];
        $existingauthor = $searchr['author'];
        $existingapilink = $searchr['apilink'];
        $backupsearch = DB::queryFirstRow("SELECT * FROM backupbooks WHERE bookid = %s", $existingIsbn);

        if ($backupsearch) {
            // Book already exists in the 'backupbooks' table, so update the record
            $result = DB::update('backupbooks', [
                'bookname' => $title,
                'author' => $author,
                'apilink' => $apilink,
            ], 'bookid=%s', $isbn);
        
            if ($result) {
                // Book updated successfully
                $response = array(
                    "success" => true,
                    "message" => "Book backup updated successfully.",
                    "isbn" => $isbn,
                    "title" => $title,
                    "author" => $author
                );
                error_log($isbn." Backup has been updated \n", 3, "../error.log");

                http_response_code(200);
            } else {
                // Failed to update book
                $response = array("success" => false, "message" => "Failed to update backup book.");
                error_log($isbn." Failed to Update Book Check for Error\n", 3, "../error.log");

                http_response_code(500);
            }
        } else {
            // Book doesn't exist in the 'backupbooks' table, so insert a new record
            $result = DB::insert('backupbooks', [
                'bookid' => $isbn,
                'bookname' => $title,
                'author' => $author,
                'apilink' => $apilink,
            ]);
        
            if ($result) {
                // Book added successfully
                $response = array(
                    "success" => true,
                    "message" => "Book added to backup successfully.",
                    "isbn" => $isbn,
                    "title" => $title,
                    "author" => $author
                );
                error_log($isbn." Has been Backed Up\n", 3, "../error.log");

                http_response_code(200);
            } else {
                // Failed to add book
                $response = array("success" => false, "message" => "Failed to add book.");
                http_response_code(500);
            }
        }

        $query = "UPDATE books SET bookname = %s, author = %s, apilink = %s WHERE bookid = %s";

        // Format the query with actual values using MeekroDB's query() method
        $result = DB::query($query, $title, $author, $apilink, $isbn);
        if ($result) {
            // Book updated successfully
            $response = array(
                "success" => true,
                "message" => "Book updated successfully.",
                "isbn" => $isbn,
                "title" => $title,
                "author" => $author
            );
            error_log($isbn." Updated from API in Books Table\n", 3, "../error.log");
            http_response_code(200);
        } else {
            // Failed to update book
            $response = array("success" => false, "message" => "Failed to update book.");
            http_response_code(500);
        }
        echo json_encode($response);
    }


   
} else {
    // If the request is not a POST request, return an error message.
    http_response_code(405); // Method Not Allowed
    echo "Error: Invalid request method. Only POST requests are allowed.";
}
?>
