<?php
include_once("inc/init.php");

// Check if the user is already logged in. If yes, redirect them to the index.php page.
if(true == Session::checkLogin()){
    header("Location: index.php");
    die();
}

// Check if the form was submitted
if(isset($_POST['submit'])){
    // Include the Database class for database operations
    include_once('../lib/Database.php');

    // Create a new Database instance
    $db = new Database();

    // Get the username and password submitted through the form (with default empty values if not set)
    $username = $_POST['username'] ?? "";
    $password = md5($_POST['password'] ?? "");

    // Query the database to check if a user with the provided username and password exists
    $result = $db->select(
        "SELECT * FROM users WHERE username = ? AND password = ?;",
        "ss",
        [$username, $password]
    );

    // If there is at least one matching user in the database
    if($result->num_rows > 0){
        // Set the user's login status to true using the Session class
        Session::setLogin(true);

		// Set user details to session
		$user = $result -> fetch_object();
		Session::set('id', $user->id);
		Session::set('username', $user->username);
		Session::set('role', $user->role);
		Session::set('first_name', $user->first_name);
		Session::set('last_name', $user->last_name);

        // Redirect the user to the index.php page
        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/css/login.css">
</head>
<body>
	<section class="login">
		<div class="login_box">
			<div class="left">
				<div class="contact">
					<form action="" method="post">
						<h3>SIGN IN</h3>
						<input type="text" name="username" placeholder="USERNAME">
						<input type="text" name="password" placeholder="PASSWORD">
						<button type="submit" name="submit" class="submit">Sign In</button>
					</form>
				</div>
			</div>
			<div class="right">
				<div class="right-text">
					<h2>LONYX</h2>
					<h5>A UX BASED CREATIVE AGENCEY</h5>
				</div>
				<div class="right-inductor"><img src="https://lh3.googleusercontent.com/fife/ABSRlIoGiXn2r0SBm7bjFHea6iCUOyY0N2SrvhNUT-orJfyGNRSMO2vfqar3R-xs5Z4xbeqYwrEMq2FXKGXm-l_H6QAlwCBk9uceKBfG-FjacfftM0WM_aoUC_oxRSXXYspQE3tCMHGvMBlb2K1NAdU6qWv3VAQAPdCo8VwTgdnyWv08CmeZ8hX_6Ty8FzetXYKnfXb0CTEFQOVF4p3R58LksVUd73FU6564OsrJt918LPEwqIPAPQ4dMgiH73sgLXnDndUDCdLSDHMSirr4uUaqbiWQq-X1SNdkh-3jzjhW4keeNt1TgQHSrzW3maYO3ryueQzYoMEhts8MP8HH5gs2NkCar9cr_guunglU7Zqaede4cLFhsCZWBLVHY4cKHgk8SzfH_0Rn3St2AQen9MaiT38L5QXsaq6zFMuGiT8M2Md50eS0JdRTdlWLJApbgAUqI3zltUXce-MaCrDtp_UiI6x3IR4fEZiCo0XDyoAesFjXZg9cIuSsLTiKkSAGzzledJU3crgSHjAIycQN2PH2_dBIa3ibAJLphqq6zLh0qiQn_dHh83ru2y7MgxRU85ithgjdIk3PgplREbW9_PLv5j9juYc1WXFNW9ML80UlTaC9D2rP3i80zESJJY56faKsA5GVCIFiUtc3EewSM_C0bkJSMiobIWiXFz7pMcadgZlweUdjBcjvaepHBe8wou0ZtDM9TKom0hs_nx_AKy0dnXGNWI1qftTjAg=w1920-h979-ft" alt=""></div>
			</div>
		</div>
	</section>
</body>

</html>