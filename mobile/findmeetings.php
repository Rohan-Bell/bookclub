<?php
    /* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    error_log("Getting to Find Meetings Page", 3, "../error.log");

    if(isset($_POST['userid']))
    $userid = $_POST['userid'];
     { //handshake with DB
        require ('../connect.php');

        //go get nominated user data
        $results = DB::query('Select * FROM meetings WHERE clubid in (SELECT clubid FROM clubmembership WHERE userid = %s)', $userid);
        echo json_encode ($results);

    }