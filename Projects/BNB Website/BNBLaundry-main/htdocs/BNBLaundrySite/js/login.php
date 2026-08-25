<?php
session_start();
include('db.php'); 		// htdocs>BNBLaundrySite>js
include('header.php');	// Include the would-be header


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// retrieve submitted username and pass
	$username = $_POST['username'];
	$password = $_POST['password'];
    
	// find the user by username
	$query = "SELECT * FROM User WHERE username='$username'";
	$result = mysqli_query($conn, $query);
    
	if (mysqli_num_rows($result) > 0) {
		$user = mysqli_fetch_assoc($result);
        
		if (password_verify($password, $user['password'])) {
			$_SESSION['username'] = $username;
			$_SESSION['role'] = $user['role'];
            	
			header("Location: index.php");
			exit();
		} else {
			echo "<div class='alert alert-danger'>Invalid username or password.</div>";
		}
	} else {
		echo "<div class='alert alert-danger'>Invalid username or password.</div>";
	}
}
?>

<br><br><br><br><br><br><br>
<h2>Login</h2>
<br>
<form method="POST" action="login.php" class="form-group">
	<label>Username:</label>
	<input type="text" name="username" class="form-control" required><br>
	<label>Password:</label>
	<input type="password" name="password" class="form-control" required><br>
	<button type="submit" class="btn btn-primary">Login</button>
</form>
<br><br><br><br><br><br>

<?php
include('footer.php');
?>
