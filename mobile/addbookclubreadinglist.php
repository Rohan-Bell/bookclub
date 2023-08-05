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

$bookid = $_POST['bookid'];
$userid = $_POST['userid'];
$clubid = $_POST['clubid'];

error_log("Page has been reached:\n", 3, "../error.log");
error_log($bookid.$userid."\n", 3, "../error.log");



$initialquery = "SELECT * FROM readinglist WHERE bookid = %s AND clubid = %s";
$result = DB::query($initialquery,$bookid,$clubid);

if ($result){
    $response = array("success" => false, "message" => "Book Already Exists in Database");
    http_response_code(400);
    echo json_encode($response);

} else{
    $addquery = "INSERT INTO readinglist (bookid, userid, clubid) 
    VALUES (%s, %s, %s)";
    $result = DB::query($addquery,$bookid,$userid, $clubid);
    $response = "Added successfully";
    echo json_encode($response);
    

}
?>
