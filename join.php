<?php
    session_start();
    //handshake with db
    require('connect.php');
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
    <!-- <script src="js/new_ajax_helper.js"></script> -->
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
            <a class="nav-link active" href="index.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="create.php">Create a Book Club</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="join.php">Join a Book Club <span class="visually-hidden">(current)</span></a>
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
            <h2>Join a Book Club</h2>
            <h3>Search for a Book Club</h3>

            <form action="join.php" method="POST">
                <div class="form-group">
                    <label for="clubsearchlabel" class="form-label mt-4">Select a club below:</label>
                    <select class="form-control" name="clubsearch" id="clubsearch" style="width:90%;">
                    <?php
                        $userid = $_SESSION['userid'];
                        //show clubs the user is not in
                        $clubs = DB::query('SELECT clubname, clubid FROM clubs WHERE clubid NOT IN (SELECT clubid FROM clubmembership WHERE userid = %s)', $userid);

                        foreach ($clubs as $club)
                        {
                            $clubid = $club['clubid'];
                            $clubname = $club['clubname'];
                            echo "<option value='".$clubid."'>".$clubname."</option>";
                        }
                    ?>                        
                </select>
                </div>
                <br/>
                <button type="submit" name="joinclub" value="joinclub" class="btn btn-primary">Join Club </button></p>
            </form>
        </div>
        <div class="col-md-2"> <!-- put in picture --> </div> 
    </div>

</body>
</html>


<?php
    //handshake with db
    require('connect.php');

    if(isset($_SESSION['userid']))
    {
        if (isset($_POST['joinclub']))
        {
            $userid = $_SESSION['userid'];
            $clubid = $_POST['clubsearch'];

            //insert the creator as a user in the club
            DB::INSERT('clubmembership', array('clubid' => $clubid,'userid' => $userid, 'role' => 'member', 'status' => 'requested' ));

            header('Location: index.php');
        }    
    } 
    else
    {
        header('Location: login.php'); //user has not logged in
    }
?>           

