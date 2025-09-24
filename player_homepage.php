<?php

session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php");
	exit();
}

if (isset($_SESSION['user_id'])) {
	$mysqli = require __DIR__ . '/database.php';
	$sql = "SELECT * FROM user WHERE id = {$_SESSION['user_id']}";
	$result = $mysqli->query($sql);
	$user = $result->fetch_assoc();
}

if ($user['role'] !== 'player') {
	header("Location: 403.php");
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Player Homepage</title>
</head>

<body>
	<?php if (isset($user)): ?>
		<h1>Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
	<?php endif; ?>
	<p>This is your homepage where you can view your character information and campaign details.</p>
	<!-- Add more player-specific content here -->

	<a href="logout.php">Logout</a>
</body>

</html>