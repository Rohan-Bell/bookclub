<?php
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Book Clubs Australia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body>

<!-- MENU -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Book Clubs Australia</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor03">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="create.php">Create a Book Club <span class="visually-hidden">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="join.php">Join a Book Club</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin.php">Book Club Admin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Log out</a>
        </li>
    </div>
  </div>
</nav>

<!-- PAGE -->
<div class="container">
    <div class="col-md-8">
        <h2>Create a Book Club</h2>

        <form name="createform" action="create.php" method="post">
            <div class="form-group">
                <label class="form-label mt-4">Book Club Name</label>
                <input type="text" class="form-control" aria-describedby="Club Name" placeholder="Enter Club Name" name="clubname" required>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">Suburb</label>
                <input type="text" class="form-control" aria-describedby="Suburb" placeholder="Enter Suburb" name="suburb" required>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">State</label>
                <select name="state" class="form-control" id="formselect">
                    <option>ACT</option>
                    <option>NSW</option>
                    <option>NT</option>
                    <option>QLD</option>
                    <option>SA</option>
                    <option>WA</option>
                    <option>TAS</option>
                    <option>VIC</option>
                </select>
            </div>
            <br/>
        <button type="submit" name="createclub" value="createclub" class="btn btn-primary">Create Club</button>
        </form>

        <?php
            //redirect
            if(!isset($_SESSION['userid']))
            {
                header('Location: login.php'); //user has not logged in
            }

            //if form submitted
            if(isset($_POST['createclub']))
            {
                //go get posted shiz and clean it up or clobber it
                $clubname = trim(stripslashes(htmlspecialchars($_POST['clubname'])));
                $suburb= trim(stripslashes(htmlspecialchars($_POST['suburb'])));
                $state = trim(stripslashes(htmlspecialchars($_POST['state'])));
                $clubadminid = $_SESSION['userid'];
            
                //handshake with db
                require('connect.php');
            
                //check to see if book club name is already used
                $row = DB::queryFirstRow('SELECT * FROM clubs WHERE clubname = %s', $clubname);

                //DO NOT ALLOW A USER TO CREATE A BOOK CLUB IF THEY ARE ALREADY AN ADMIN OF ANOTHER
            
                if (!$row) //clubname not already in use
                {
                    //insert the club with the creator as the admin
                    DB::INSERT('clubs', array('clubname' => $clubname,'suburb' => $suburb,'state'=> $state));

                    //get the new club id
                    $clubid = DB::queryFirstField('SELECT clubid FROM clubs WHERE clubname = %s', $clubname);

                    //insert the creator as a user in the club
                    DB::INSERT('clubmembership', array('clubid' => $clubid,'userid' => $clubadminid, 'role' => 'admin', 'status' => 'approved' ));
                    
                    //update creators permission in the database to be a club admin
                    DB::UPDATE('users', ['permission' => 'clubadmin'], "userid=%s", $clubadminid);

                    $_SESSION['permission'] = 'clubadmin'; //upgrade their permission in the session
                    header('Location: admin.php'); //redirect to the club admin page

                } else {
                    echo '<p>Club name not available, try again!!</p>';
                }
            } 
        ?>


    </div>
    <div class="col-md-2"> <!-- put in picture --> </div> 

</div>
</body>
</html>
