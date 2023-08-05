<?php
    /* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    //error_log("Error message", 3, "../error.log");

    //$gsearch = $_POST['search'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    // $isbn = $_POST['isbn'];
     $subject =$_POST['subject'];
    // $oclc = $_POST['oclc'];
    // $contributor = $_POST['contributor'];
   // $gsearch ="book";
    $response = null;

    if ($title || $author || $_POST['isbn'] || $_POST['subject'] || $_POST['oclc'] || $_POST['contributor']) { // check if any parameter has a value
        $url = "https://openlibrary.org/search.json?";
        $apiLink = "";
        
        if ($author) {
            $url .= "author=" . urlencode($author) . "&";
        }
    
        if ($title) {
            $url .= "title=" . urlencode($title) . "&";
        }
    
        if ($_POST['isbn']) {
            $isbn = urlencode($_POST['isbn']);
            $url .= "isbn=" . $isbn . "&";
            $apiLink = "https://openlibrary.org/isbn/" . $isbn . ".json";
        }
    
        if ($_POST['oclc']) {
            $url .= "oclc=" . urlencode($_POST['oclc']) . "&";
        }
    
        if ($_POST['contributor']) {
            $url .= "contributor=" . urlencode($_POST['contributor']) . "&";
        }
    
        if ($_POST['subject']) {
            $url .= "subject=" . urlencode($_POST['subject']) . "&";
        }
    
        // Remove the trailing '&' if no additional parameters are present
        $url = rtrim($url, "&");
    
        // Set the options for the context
        $options = array(
            'http' => array(
                'header'  => $headers,
                'method'  => 'GET'
            )
        );
        
        // Create the context and pass the options
        $context  = stream_context_create($options);
        $bookjson = json_decode(file_get_contents($url, false, $context));
        echo json_encode($bookjson);
    }
?>

