<?php
    // Connect to the MySQL server
    $servername = "https://book-club-s3117571445035.codeanyapp.com/phpmyadmin";
    $username = "root";
    $password = "";
    $database = "Fuck You";
    
    $conn = new mysqli($servername, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Insert the entry 100 times
    $message = "Fuck You, Nic";
    for ($i = 0; $i < 100; $i++) {
        $sql = "INSERT INTO entries (message) VALUES ('$message')";
        if ($conn->query($sql) === false) {
            echo "Error inserting entry: " . $conn->error;
        }
    }
    
    // Close the connection
    $conn->close();
    
    ?>
?>