<?php
session_start();
?>

<html>
	<head></head>
	<body>
		<?php
		
		if(array_key_exists('nonce', $_POST))
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
			
			if($connected)
			{
				verify($conn);
				$conn->close();
			}
		}
		
		function verify($conn)
		{
			//just update entry in db with verify bit set to 1
			if (isset($_SESSION["email"])) //make sure cookie exists
			{
				$email = $_SESSION["email"];
				$input = $_POST["nonce"];
				echo "\n$email";

				$stmt = $conn->prepare("Select nonce From Users Where email = ?");
				$stmt->bind_param("s", $email);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_row();
				$nonce = $row[0];

				if ($input = $nonce) //make sure user gave right code
				{
					//I want separate path that makes the user logged in... how??
					if (isset($_SESSION["approved"]) && $_SESSION["approved"] == true)
					{
						echo $_SESSION["approved"];
						$_SESSION["login"] = true;
						//set this to false with a log out button on user page
						return;
					}

					else
					{
						echo '\nCheck good';
						$stmt = $conn->prepare("Update Users Set verify = 1 Where email = ?");
						$stmt->bind_param("s", $email);
						$result = $stmt->execute();

						if ($result)
						{
							'Update good';
							echo '<script>alert("Verification success!")</script>';
							$stmt = $conn->prepare("Update Users Set nonce = null Where email = ?");

							$stmt->bind_param("s", $email);
							$stmt->execute();
							$result = $stmt->get_result();
							if ($result) { echo 'null done'; }

							session_unset();
							return;
						}
						if (!$result) { echo '<script>alert("Verification failed.")</script>'; return; }
					}
				}
				if ($input != $nonce)
				{
					echo '<script>alert("Code incorrect.")</script>';
					return;
				}
			}
			if (!isset($_SESSION["email"]))
			{
				echo $_SESSION["email"];
				echo '<script>alert("Verification failed. Email var not set.")</script>';
				return;
			}
		}
		?>
		<div>
            <form id="verify" action="verify.php" method="post">
                <label>Verification Code: </label>
                <input type="text" name="nonce">
                <br/>
                <input type="submit" name="nonce" class="button" value="Submit" />
            </form>
		</div>
	</body>
</html>