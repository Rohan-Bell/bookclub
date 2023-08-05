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
            <span class="visually-hidden">(current)</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="create.php">Create a Book Club</a>
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
        
        <?php
            if(isset($_SESSION['userid']))
            {
                echo "<h4>Welcome ".$_SESSION['name']."</h4><br>";
                echo "<h2>Upcoming Book Club Meetings</h2>";

                //query the membership table to find out the book clubs, they are a member of
                //handshake with db
		        require('connect.php');

                $userid = $_SESSION['userid'];

                //Output each Book club the user is a member of and then....
                $clubdata = DB::query('SELECT clubs.clubname, clubs.clubid FROM clubs INNER JOIN clubmembership ON clubs.clubid = clubmembership.clubid WHERE clubmembership.userid = %s', $userid);

                if (!empty($clubdata))
                {
                    require('output_table.php');
                    foreach ($clubdata as $club)
                    {
                        $clubname = $club['clubname'];
                        $clubid = $club['clubid'];

                        echo "<h3>".$clubname."</h3>";

                        //Output all book clubs meeting involving the user
                        $meetingsdata = DB::query('SELECT meetings.meetingtime, meetings.meetinglocation, books.bookname, books.author FROM meetings INNER JOIN books on meetings.chosenbookid = books.bookid WHERE meetings.clubid = %s AND meetings.meetingtime > CURRENT_TIMESTAMP', $clubid);
                        
                        if (!empty($meetingsdata))
                        {
                            echo '<form action="index.php" method="POST"> ';
                            echo '<input type="hidden" name="clubid" value="'.$clubid.'">';
                            echo output_table($meetingsdata, false);
                            echo '<button type="submit" class="btn btn-primary" name="leaveclub" id="leaveclub">Leave Club</button>';
                            echo '</form><br>';
                        } else
                        {
                            echo "No upcoming meetings";
                        }
                    }

                    //only not club admins can leave a club
                    if (isset($_POST['leaveclub']))
                    {
                        $clubid = $_POST['clubid'];
                        DB::query('DELETE FROM clubmembership WHERE userid=%s AND clubid=%s AND NOT role="admin"', $userid, $clubid);

                        unset($_POST['leaveclub']);
                    }

                } else {
                    echo "You do not belong to any clubs!"; 
                }

                //if user is the book club admin ???

            } 
            else
            {
                header('Location: login.php'); //user has not logged in
            }
        ?>
    </div>
    <div class="col-md-2"> <!-- put in picture --> </div> 

</div>
</body>
</html>
