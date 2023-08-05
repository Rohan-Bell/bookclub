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
            <a class="nav-link" href="register.php">Register</a>
            </li>
        </div>
    </div>
    </nav>

    <!-- PAGE -->
    <div class="container">
        <div class="col-md-8">
        <h2>Register for Book Clubs Australia</h2> 

        <form name="registerform" action="register.php" method="post">
            <div class="form-group">
                <label class="form-label mt-4">First Name: </label>
                <input type="text" class="form-control" aria-describedby="First Name" placeholder="Enter First Name" name="firstname" required>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">Last Name: </label>
                <input type="text" class="form-control" aria-describedby="Last Name" placeholder="Enter Last Name" name="lastname" required>
            </div>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">Email: </label>
                <input type="email" class="form-control" aria-describedby="Email" placeholder="Enter Email" name="email" required>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">Password: </label>
                <input type="password" class="form-control" aria-describedby="Password" placeholder="Enter Password" name="password" required>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">Confirm Password: </label>
                <input type="password" class="form-control" aria-describedby="Confirm Password" placeholder="Confirm Password" name="passwordconfirm" required>
            </div>
            <div class="form-group">
                <label class="form-label mt-4">Post code: </label>
                <input type="text" class="form-control" aria-describedby="Post code" placeholder="Enter Post code" name="postcode" required>
            </div>
            <br/>
        <button type="submit" name="register" value="register" class="btn btn-primary">Submit</button> <p><a href="login.php">Login</a></p>
        </form>

        <div class="col-md-2"> <!-- put in picture --> </div> 
    </div>


    <?php
        //redirect
        if(isset($_SESSION['userid']))
        {
            header('Location: index.php'); //user must log out first before being able to register
        }

        //posted
	    else if (isset($_POST['register'])){
		//registration attempt
		//go get posted shiz and clean it up or clobber it
		$email = trim(stripslashes(htmlspecialchars($_POST['email'])));
		$firstname = trim(stripslashes(htmlspecialchars($_POST['firstname'])));
		$lastname = trim(stripslashes(htmlspecialchars($_POST['lastname'])));
        $postcode = trim(stripslashes(htmlspecialchars($_POST['postcode'])));
		$password = $_POST['password'];
		$passwordconfirm = $_POST['passwordconfirm']; 

		//handshake with db
		require('connect.php');

		//check to see if username is used
		$row = DB::queryFirstRow('select * from users where email = %s',$email);

		if (!$row){
			//that user must be a new user
			//final step - check passwords
			if ($password == $passwordconfirm){
				//good to go
				//insert user
				DB::insert('users', array(
	  				'email' => $email,
	  				'firstname' => $firstname,
	  				'lastname' => $lastname,
                    'postcode' => $postcode,
	  				'password' => sha1($password))
				);
				header('Location: login.php');

			} else {
				//passwords do not match
				echo '<p>Passwords do not match, try again</p>';
			}
		} else {
			//duplicate user - how best to feed this back?
			echo '<p>User not available, try again</p>';
		}
	}
    ?>

</body>
</html>
