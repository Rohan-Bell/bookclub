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

error_log("Search Admin Book page has been reached:\n", 3, "../error.log");
error_log($userid."\n", 3, "../error.log");


$initialquery = "SELECT clubid FROM clubmembership WHERE userid = %s AND status = 'approved' AND role = 'admin'";
$result = DB::query($initialquery, $userid);

if ($result) {
    $response = array();
    foreach ($result as $row) {
        $clubid = $row['clubid'];
        $query = "SELECT clubid, clubname, suburb, state FROM clubs WHERE clubid = %s AND status = 'approved'";
        $clubData = DB::queryFirstRow($query, $clubid);
        if ($clubData) {
            $response[] = $clubData;
        }
    }
    echo json_encode($response);
} else {
    $response = array("success" => false, "message" => "User has no clubs");
    http_response_code(400);
    echo json_encode($response);
}
?>
