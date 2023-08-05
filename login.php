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
    </div>
  </div>
</nav>


<!-- PAGE -->
<div class="container">
    <div class="col-md-8">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="form-group">
            <label for="emaillable" class="form-label mt-4">Email address</label>
            <input type="email" class="form-control" id="email" aria-describedby="email" placeholder="Enter email" name="email" required>
            </div>
            <div class="form-group">
            <label for="passwordlabel" class="form-label mt-4">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
            </div>
        <br/>
        <button type="submit" name="login" value="login" class="btn btn-primary">Submit </button></p> <p><a href="register.php">Register</a></p>
        </form>
        <p><b>Admin</b>: admin@admin, Password: password</p>
        <p><b>Club Admin</b>: clubadmin@clubadmin, Password: password</p>
        <p><b>Member</b>: member@member, Password: password</p>
    </div>
    <div class="col-md-2"></div> 
<?php
    if(isset($_POST['login']))
    {
        //attempted a login

        //retrieve and cleanup user input
        $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
        $password = sha1($_POST['password']);

        //handshake with db
        require ('connect.php');

        //go get nominated users data
        $row = DB::queryFirstRow('select * from users where email = %s',  $email);

        //how good a choice?
        if (!$row){
            //nothing came back, therefore username not registered
            echo '<p>Error with username or password</p>';
            //burn them, burn them all
            session_destroy();

        } else {
            //got a live user, let's check password
            if($password == $row['password']){
                //huzzah, user knows their password, all good
                echo '<h3>Welcome '.$row['firstname'].'</h3>';

                //account keeping for other pages
                $_SESSION['email'] = $row['email'];
                $_SESSION['name'] = $row['firstname'].' '.$row['lastname'];
                $_SESSION['userid'] = $row['userid'];
                $_SESSION['permission'] = $row['permission'];

                header('Location: index.php');

            } else {
                //password error
                echo '<p>Error with username or password</p>';
                //burn them, burn them all
                session_destroy();
            }
        }
    }
?>

</div>
</body>
</html>