<?php
	session_start();
?>

<html>
	<head></head>
	<body>
		<?php
		if(array_key_exists('submit', $_POST))
		{
			change();
		}
		
		function change()
		{
		
			$host = 'mysql';
			$user = 'root';
			$pass = 'rootpassword';
			$db = 'ProjectB';
			$connected = true;

			// Create connection
			echo "Attempting to connect...";
			$conn = new mysqli($host, $user, $pass, $db);

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_errno . $conn->connect_error);
				echo '<script>alert("Connection failed.")</script>';
				$connected = false;
			}
			echo "Connected successfully";

			if ($connected)
			{
				$email = $_SESSION["email"];
				$userpswd = password_hash($_POST["pswd"], PASSWORD_BCRYPT);

				//update sql entry with new pswd, using email as primary key
				//display success or failure message and give reload or login links as appropriate
				$stmt = $conn->prepare("Update Users Set password = ? Where email = ?");
				$stmt->bind_param("ss", $userpswd, $email);
				$result = $stmt->execute();

				if ($result) { echo '<script>alert("Reset success!")</script>'; }
				if (!$result) { echo '<script>alert("Reset failed.")</script>'; }
			}
		}
		?>
		<div>
            <form id="reset" action="resetpswd.php" method="post">
                <label>New Password: </label>
                <input type="text" name="pswd">
                <br/>
                <input type="submit" name="submit" class="button" value="Submit" />
            </form>
		</div>
	</body>
</html>