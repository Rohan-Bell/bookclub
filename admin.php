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
          <a class="nav-link active" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="create.php">Create a Book Club</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="join.php">Join a Book Club</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin.php">Book Club Admin <span class="visually-hidden">(current)</span> </a>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Log out</a>
        </li>
        </li>
    </div>
  </div>
</nav>

<!-- PAGE -->
<div class="container">
    <div class="row">
    <div class="col-sm-8">
    <?php
        $permission = $_SESSION['permission'];
        if(isset($permission)) 
        {
            require('connect.php');
            require('output_table.php');

            if ($permission == 'admin')
            {
                echo "<h2>Admin</h2><br>";
                echo "<h3>Users</h3>";
                $userdata = DB::query('SELECT userid, firstname, lastname, email FROM users');

                //remove admin from users
                array_shift($userdata);

                if (empty($userdata)){
                    echo "<p>No user data found!</p>";
                } else {
                    echo "<form name='users' action='' method='POST'>";
                    echo output_table($userdata, true);
                    echo "<button type='submit' name='deleteusers' value='deleteusers' class='btn btn-primary'>Delete</button>";
                    echo "</form><br>";
                }

                //output all book clubs
                echo "<br><h3>Book Clubs</h3>";
                $clubsdata = DB::query('SELECT * FROM clubs');
                if (empty($clubsdata)){
                    echo "<p>No clubs data found!</p>";
                } else {
                    echo "<form name='clubs' action='admin.php' method='POST'>";
                    echo output_table($clubsdata, true);
                    echo "<button type='submit' name='approveclubs' value='approveclubs' class='btn btn-secondary'>Approve Clubs</button> ";
                    echo " <button type='submit' name='deleteclubs' value='deleteclubs' class='btn btn-primary'>Delete Clubs</button>";
                    echo "</form><br>";
                }

                //output all book club meetings
                echo "<br><h3>Book Club Meetings</h3>";
                $meetingsdata = DB::query('SELECT * FROM meetings');
                if (empty($meetingsdata)){
                    echo "<p>No meetings data found!</p>";
                } else {
                    echo "<form name='meetings' action='admin.php' method='POST'>";
                    echo output_table($meetingsdata, true);
                    echo "<button type='submit' name='deletemeetings' value='deletemeetings' class='btn btn-primary'>Delete Meetings</button>";
                    echo "</form><br>";
                }
                echo "</div>";
                echo "<div class='col-md-2'>";
                echo "</div>";


                //ADMIN HANDLES FOR POST REQUESTS
                //---------------------------------------------
                if (isset($_POST['deleteclubs']))
                {
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::query('DELETE FROM clubmembership WHERE clubid=%s', $id);
                        DB::query('DELETE FROM clubs WHERE clubid=%s', $id);
                    }
                    unset($_POST['deleteclubs']);
                } 
                else if (isset($_POST['deleteusers']))
                {
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::query('DELETE FROM clubmembership WHERE userid=%s', $id);
                        DB::query('DELETE FROM users WHERE userid=%s', $id);
                    }
                    unset($_POST['deleteusers']);
                } 
                else if (isset($_POST['approveclubs']))
                {
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::UPDATE('clubs', ['status' => 'approved'], "clubid=%s", $id);
                    }
                    unset($_POST['approveclubs']);
                } 
                else if (isset($_POST['deletemeetings']))
                {
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::query('DELETE FROM meetings WHERE meetingid=%s', $id);
                    }
                    unset($_POST['deletemeetings']);
                }
            } 
            else if ($permission == 'clubadmin')
            {
                echo "<h2>Book Club Admin</h2><br>";

                $userid = $_SESSION['userid'];
                $clubsdata = DB::query('SELECT clubs.clubid, clubs.clubname FROM clubs INNER JOIN clubmembership ON clubs.clubid = clubmembership.clubid WHERE clubmembership.userid = %s AND clubmembership.role = "admin"', $userid);

                foreach ($clubsdata as $club)
                {
                    $clubid = $club['clubid'];
                    $clubname = $club['clubname'];

                    echo "<h3>".$clubname."</h3><br>";

                    $userdata = DB::query('SELECT users.userid, users.firstname, users.lastname, users.email, clubmembership.status FROM users INNER JOIN clubmembership ON users.userid = clubmembership.userid WHERE clubmembership.clubid = %s', $clubid);

                    if (empty($userdata)){
                        echo "<p>No user data found!</p>";
                    } else {
                        echo "<form name='users' action='admin.php' method='POST'>";

                        //Include a hidden field for each club
                        echo "<input type='hidden' name='clubid' value='".$clubid."' >";

                        echo output_table($userdata, true); //output selectable table

                        echo "<button type='submit' name='approveusers' value='approveusers' class='btn btn-secondary'>Approve Users</button> ";
                        echo " <button type='submit' name='adminusers' value='adminusers' class='btn btn-secondary'>Make User Admin</button> ";
                        echo " <button type='submit' name='removeusers' value='removeusers' class='btn btn-primary'>Remove User</button>";
                        echo "<br><br><button type='submit' name='deleteclub' value='".$clubid."' class='btn btn-danger'>Delete Club</button>";

                        echo "</form><br>";
                    }

                    //output all book club meetings
                    echo "<h3>Book Club Meetings</h3>";
                    $meetingsdata = DB::query('SELECT * FROM meetings WHERE clubid = %s ORDER BY meetingtime DESC', $clubid);
                    if (empty($meetingsdata)){
                        echo "<p>No meetings found!</p>";
                    } else {
                        echo "<form name='meetings' action='admin.php' method='POST'>";
                        //Include a hidden field  for each club
                        echo "<input type='hidden' name='clubid' value='".$clubid."' >";
                        echo output_table($meetingsdata, true);
                        echo "<button type='submit' name='deletemeetings' value='deletemeetings' class='btn btn-primary'>Delete Meetings</button>";
                        echo "</form><br>";
                    }
                }

                echo "</div>";
                echo "<div class='col-sm-4'>";
                //create a meeting time
                echo "<div class='sideblock'>";
                echo '<h3>Add a Book Club Meeting</h3>';
                echo '<form name="meetingsform" action="admin.php" method="post">';

                echo '<div class="form-group">
                    <label class="form-label mt-4">Book Club: </label>
                    <select name="bookclubselect" id="bookclubselect">';

                foreach ($clubsdata as $club)
                {
                    $clubid = $club['clubid'];
                    $clubname = $club['clubname'];
                    echo '<option value="'.$clubid.'">'.$clubname.'</option>';
                }

                echo '</select></div>';
                echo '<div class="form-group">
                    <label class="form-label mt-4">Meeting Time: </label>
                    <input type="datetime-local" class="form-control" aria-describedby="First Name" placeholder="Enter Meeting Time" name="meetingtime" required>
                </div>
                <div class="form-group">
                    <label class="form-label mt-4">Meeting Location: </label>
                    <input type="text" class="form-control" aria-describedby="Meeting Location" placeholder="Enter Meeting Location" name="meetinglocation" required>
                </div>
                <div class="form-group">
                    <label class="form-label mt-4">Book Name: </label>
                    <input type="text" class="form-control" aria-describedby="Email" placeholder="Enter Book Name" name="bookname" required>
                </div>
                <div class="form-group">
                    <label class="form-label mt-4">Book Author: </label>
                    <input type="text" class="form-control" aria-describedby="Book Author" placeholder="Enter Book Author" name="author" required>
                </div><br><br>';
                echo "<button type='submit' name='createmeeting' value='createmeeting' class='btn btn-secondary'>Create Meeting</button>";
                echo "</form></div></div>";


                //BOOKADMIN HANDLES FOR POST REQUESTS
                //------------------------------------------------
                //create a meeting
                if (isset($_POST['createmeeting']))
                {
                    $clubid = $_POST['bookclubselect'];
                    $meetingtime = $_POST['meetingtime'];
                    $meetinglocation = $_POST['meetinglocation'];
                    $bookname = $_POST['bookname'];
                    $author = $_POST['author'];

                    $bookid = DB::queryFirstField('SELECT bookid FROM books WHERE bookname = %s AND author = %s', $bookname, $author);

                    if (!$bookid) //insert book if not exist
                    {
                        DB::insert('books', array(
                            'bookname' => $bookname,
                            'author' => $author)
                        );
                        $bookid = DB::queryFirstField('SELECT bookid FROM books WHERE bookname = %s AND author = %s', $bookname, $author);
                    }

                    DB::insert('meetings', array(
                        'clubid' => $clubid,
                        'meetingtime' => $meetingtime,
                        'meetinglocation' => $meetinglocation,
                        'chosenbookid' => $bookid)
                    );

                    unset($_POST['createmeeting']);
                    header('Location: admin.php');
                } 
                else if (isset($_POST['adminusers']))
                {
                    $clubid = $_POST['clubid'];
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::UPDATE('users', ['permission' => 'clubadmin'], "userid=%s", $id);
                        DB::UPDATE('clubmembership', ['role' => 'clubadmin'], "userid=%s AND clubid=%s", $id, $clubid);
                    }

                    unset($_POST['adminusers']);
                } 
                else if  (isset($_POST['removeusers']))
                {
                    $clubid = $_POST['clubid'];
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::query('DELETE FROM clubmembership WHERE userid=%s AND clubid=%s AND NOT role="admin"', $id, $clubid);
                    }

                    unset($_POST['removeusers']);
                } 
                else if  (isset($_POST['approveusers']))
                {
                    $clubid = $_POST['clubid'];
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::UPDATE('clubmembership', ['status' => 'approved'], "userid=%s AND clubid=%s", $id, $clubid);
                    }

                    unset($_POST['approveusers']);
                } 
                else if (isset($_POST['deleteclub']))
                {
                    $clubid = $_POST['clubid'];
                    $selectedids = $_POST['selectedids'];
                    DB::query('DELETE FROM clubs WHERE clubid=%s', $clubid);
                    DB::query('DELETE FROM clubmembership WHERE clubid=%s', $clubid);

                    unset($_POST['deleteclub']);
                } 
                else if (isset($_POST['deletemeetings']))
                {
                    $clubid = $_POST['clubid'];
                    $selectedids = $_POST['selectedids'];
                    for($i=0; $i < count($selectedids); $i++)
                    {
                        $id = $selectedids[$i];
                        DB::query('DELETE FROM meetings WHERE clubid=%s and meetingid=%s', $clubid, $id);
                    }

                    unset($_POST['deletemeetings']);
                }
            }
            else 
            {
                header('Location: login.php');
            }
        } else
        {
            header('Location: login.php'); //user has not logged in
        }
        ?>

        </div>
    </div>
</body>
</html>

