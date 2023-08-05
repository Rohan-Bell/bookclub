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
        // Got a live user, let's check the password
        // To check the password, we need to decrypt the client-side encrypted password and compare it
        // to the hashed password stored in the database

        // Generate the Data Encryption Key (DEK) using the same key used on the client-side for encryption
        $key = "MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAOt+ZYHYpjb3tepG\
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

        // Decrypt the password using the DEK and AES-GCM decryption
        $decryptedPassword = openssl_decrypt(base64_decode($password), 'aes-256-gcm', base64_decode($key), OPENSSL_RAW_DATA, substr(base64_decode($key), 0, 16));

        // Hash the decrypted password using SHA-1 (as done on the client-side)
        $hashedPassword = sha1($decryptedPassword);

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
