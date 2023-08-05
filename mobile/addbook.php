<?php
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (for testing purposes)
header("Access-Control-Allow-Methods: POST"); // Allow only POST requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow only Content-Type header

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight request
    http_response_code(200);
    exit;
}

require_once 'meekrodb.2.3.class.php';

// Connect info
DB::$user = 'root';
DB::$password = '';
DB::$dbName = 'bookreview';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the book information from the form data
    $title = trim(stripslashes(htmlspecialchars($_POST['title'])));
    $author = trim(stripslashes(htmlspecialchars($_POST['author'])));
    $isbn = trim(stripslashes(htmlspecialchars($_POST['isbn'])));

    // Validate the received data (you can add more validation as per your requirements)
    if (empty($title) || empty($author) || empty($isbn)) {
        // Return an error response
        http_response_code(400);
        echo json_encode(array("error" => "Incomplete book data."));
        exit;
    }

    // Perform any validation or sanitization of the data if required.
    // For example, you can use functions like htmlspecialchars() or mysqli_real_escape_string()

    // Prepare the SQL INSERT query with placeholders
    $sql = "INSERT INTO books (title, author, bookid) VALUES (%s, %s, %s)";

    // Execute the prepared statement
    $result = DB::query($sql, $title, $author, $isbn);

    if ($result) {
        // Book added successfully
        http_response_code(200);
        echo json_encode(array("message" => "Book added successfully."));
    } else {
        // Failed to add book
        http_response_code(500);
        echo json_encode(array("error" => "Failed to add book."));
    }
} else {
    // If the request is not a POST request, return an error message.
    http_response_code(405); // Method Not Allowed
    echo "Error: Invalid request method. Only POST requests are allowed.";
}
?>
