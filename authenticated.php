<?php
session_start();
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'docker';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['email'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('No me intentes hackear!');
    header( "Refresh:5; url=../index.php", true, 303);
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password FROM usuarios WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $email=mysqli_real_escape_string($con,$_POST['email']);
	$stmt->bind_param('s', $email);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        $jamon=mysqli_real_escape_string($con,$_POST['password']);
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if($jamon == $password) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.

            $md5=md5($email);
            setcookie("name", $md5, time()+3600);
            
            $sql = "INSERT INTO sesiones (sesion, email) VALUES ('$md5','$email')";
    if (mysqli_query($con, $sql)) {

      } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
      }
    
    }
            
            echo 'Bienvenido ' . $email . '!';
            echo '<br>';
            echo '<a href="../administracion.php">Acceder a wordpress</a>';
        } else {
            // Incorrect password
            echo 'Usuario o contraseña incorrecto!';
            header( "Refresh:5; url=../index.php", true, 303);
        }
    } else {
        // Incorrect username
        echo 'Usuario o contraseña incorrecto!';
        header( "Refresh:5; url=../index.php", true, 303);
    }

	$stmt->close();

?>