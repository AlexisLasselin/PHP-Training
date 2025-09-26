<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php");
	exit();
}

$mysqli = require __DIR__ . '/database.php';
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user['role'] !== 'dm') {
	header("Location: 403.php");
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>DM Homepage</title>
</head>

<body>
	<header>
		<?php if (isset($user)): ?>
			<h1>Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
		<?php endif; ?>
		<a href="logout.php">Logout</a>
	</header>

	<p>This is your homepage where you can manage your campaigns and players.</p>

	<h2>Your Campaigns</h2>
	<div id="campaign-list">
		<?php
		$sql = "SELECT * FROM campaigns WHERE dm_id = ?";
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param("i", $_SESSION['user_id']);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0):
			while ($campaign = $result->fetch_assoc()):
		?>
				<div class="campaign-item" data-campaign-id="<?= $campaign['campaign_id'] ?>">
					<h3><?= htmlspecialchars($campaign['name']) ?></h3>
					<p><?= htmlspecialchars($campaign['description']) ?></p>
					<img src="./images/corbeille.png"
						alt="Delete Campaign"
						style="width:20px;height:20px;cursor:pointer;"
						onclick="deleteCampaign(<?= $campaign['campaign_id'] ?>)">
				</div>


			<?php endwhile;
		else: ?>
			<p>You have no campaigns.</p>
		<?php endif; ?>
	</div>

	<button onclick="openModal()">Create a New Campaign</button>

	<div id="campaignModal" class="modal" style="display:none;">
		<div class="modal-content">
			<span class="close-btn" onclick="closeModal()">&times;</span>
			<h2>Create a Campaign</h2>
			<form id="campaignForm" method="post" action="create_campaign.php">
				<label for="name">Campaign Name:</label>
				<input type="text" name="name" id="name" required><br><br>

				<label for="description">Description:</label>
				<textarea name="description" id="description" required></textarea><br><br>

				<label for="date">Start Date:</label>
				<input type="datetime-local" name="date" id="date" required><br><br>

				<button type="submit">Create</button>
			</form>
			<p id="message"></p>
		</div>
	</div>

	<script>
		function openModal() {
			document.getElementById('campaignModal').style.display = 'flex';
		}

		function closeModal() {
			document.getElementById('campaignModal').style.display = 'none';
		}

		async function deleteCampaign(id) {
			if (!confirm("Are you sure you want to delete this campaign?")) return;

			try {
				const response = await fetch(`delete_campaign.php?id=${id}`);
				const data = await response.json();

				if (data.success) {
					const item = document.querySelector(`[data-campaign-id="${id}"]`);
					if (item) item.remove();
				} else {
					alert("Error: " + (data.error || "Could not delete campaign"));
				}
			} catch (e) {
				alert("An unexpected error occurred.");
			}
		}
	</script>

</body>

</html>