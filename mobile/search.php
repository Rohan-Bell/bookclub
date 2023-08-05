<?php
    /* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

  $search = $_POST['search'];

    if ($search) { // check if any parameter has a value
        $url = "https://openlibrary.org/search.json?";

    
        if ($search) {
            $url .= "q=" . urlencode($search);
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

