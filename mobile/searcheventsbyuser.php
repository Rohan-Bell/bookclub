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
//$_POST['userid'] = 2;
$userid = $_POST['userid'];

error_log("Search Events By User page has been reached:\n", 3, "../error.log");
error_log($userid."\n", 3, "../error.log");


$initialquery = "SELECT m.meetingid, m.meetinglocation, m.meetingtime, m.latitude, m.longitude, b.bookname, b.author, b.bookid
FROM meetings m
JOIN clubmembership cm ON m.clubid = cm.clubid
JOIN books b ON m.bookid = b.bookid
WHERE cm.userid = %s AND m.meetingtime >= '2020-08-02 12:00:00'
ORDER BY m.meetingtime;";
$result = DB::query($initialquery, $userid);

if ($result) {
    
    $response[] = $result;

    echo json_encode($response);
} else {
    $response = array("success" => false, "message" => "User has no Events");
    http_response_code(400);
    echo json_encode($response);
}
?>
