<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css">
	<title>DnD Login</title>
</head>
<body>
	<h1>Welcome to the DnD Login Page</h1>
	<p>Please enter your credentials to log in.</p>
	<form action="index.php" method="post">
		<label for="mail">Mail:</label>
		<input type="email" id="mail" name="mail" required><br><br>
		<label for="password">Password:</label>
		<input type="password" id="password" name="password" required><br><br>
		<input type="submit" value="Login">
	</form>
</body>
</html>

<?php
echo $_POST['username']."<br>";
echo $_POST['password']."<br>";
?>