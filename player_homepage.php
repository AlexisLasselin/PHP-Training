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
	<header>
		<?php if (isset($user)): ?>
			<h1>Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
		<?php endif; ?>
		<a href="logout.php">Logout</a>
	</header>

	<p>This is your homepage where you can view your character information and campaign details.</p>
	<!-- Add more player-specific content here -->

	<h2>Your campaigns</h2>
	<div id="your-campaigns">
		<?php
		$mysqli = require __DIR__ . '/database.php';
		$sql = "SELECT c.* FROM campaigns c
				JOIN registration r ON c.campaign_id = r.campaign_id
				WHERE r.player_id = {$user['id']}
				";

		$result = $mysqli->query($sql);

		if ($result->num_rows > 0):
			while ($campaign = $result->fetch_assoc()):
		?>
				<div class="campaign-item">
					<h3><?= htmlspecialchars($campaign['name']) ?></h3>
					<p><?= htmlspecialchars($campaign['description']) ?></p>
					<p>Starting Date: <?= htmlspecialchars($campaign['starting_date']) ?></p>
					<form action="leave_campaign.php" method="POST">
						<input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id'] ?>">
						<button type="submit" onclick="return confirm('Are you sure you want to unregister from this campaign?');">
							Unregister
						</button>

					</form>
				</div>

		<?php
			endwhile;
		else:
			echo "<p>You are not registered in any campaigns yet.</p>";
		endif;
		?>

		<h2>Join a Campaign</h2>
		<div id="campaign-list">
			<?php
			$mysqli = require __DIR__ . '/database.php';
			$sql = "SELECT * FROM campaigns 
				WHERE starting_date > NOW() 
				AND campaign_id NOT IN (
					SELECT campaign_id 
					FROM registration 
					WHERE player_id = {$user['id']}
				)
				";

			$result = $mysqli->query($sql);

			if ($result->num_rows > 0):
				while ($campaign = $result->fetch_assoc()):
			?>
					<div class="campaign-item" data-campaign-id="<?= $campaign['campaign_id'] ?>">
						<h3><?= htmlspecialchars($campaign['name']) ?></h3>
						<p><?= htmlspecialchars($campaign['description']) ?></p>
						<p>Starting Date: <?= htmlspecialchars($campaign['starting_date']) ?></p>
						<form action="join_campaign.php" method="POST">
							<input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id'] ?>">
							<button type="submit">Join Campaign</button>
						</form>
					</div>

			<?php
				endwhile;
			else:
				echo "<p>No campaigns available at the moment.</p>";
			endif;
			?>

</body>

</html>