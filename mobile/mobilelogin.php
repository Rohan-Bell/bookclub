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

if (isset($_POST['email']) && isset($_POST['password'])) {
    // Attempted a login
    $email = $_POST['email'];
    $password = $_POST['password'];
    error_log("Mobile Login Page Reached:\n", 3, "../error.log");
    error_log($email."\n", 3, "../error.log");
    // Handshake with the database
    require('../connect.php');

    // Go get nominated users' data
    $row = DB::queryFirstRow('SELECT * FROM users WHERE email = %s', $email);

    $response = null;

    if (!$row) {
        // Nothing came back, therefore the username is not registered
        $response = array("result" => "Error", "message" => "No user found!");
        echo json_encode($response);
    } else {
        error_log("User Found:\n", 3, "../error.log");
        $hashedPassword = sha1($password);
        if ($hashedPassword == $row['password']) {
            $response = array("result" => "Success", "username" => ($row['firstname'] . ' ' . $row['lastname']), "userid" => $row['userid']); // Password correct
            echo json_encode($response); // Return response to mobile
        } else {
            $response = array("result" => "Error", "message" => "Password incorrect!");
            echo json_encode($response); // Return response to mobile
        }
    }
} else {
    $response = array("result" => "Error", "message" => "Email or password field not found!");
    echo json_encode($response);
}
?>
