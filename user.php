<?php
	session_start();
?>

<html>
	<head>
		<title></title>
		<!--<link rel="stylesheet" href="user.css">-->
		<style>
			*
			{
				background-color: aquamarine;
			}

			div
			{
				width: 47vw;
				height: 90vh;
				background-color: white;
			}

			#top
			{
				height: 20px;
				background-color: aquamarine;
				padding-bottom: 1vh;
			}

			button
			{
				background-color: whitesmoke;
			}

			input
			{
				background-color: white;
			}

			form
			{
				float: right;
				padding-right: 30vw;
			}

			form#info
			{
				background-color: white;
			}

			form#info>button, form#info>input
			{
				margin-bottom: 1vh;
			}

			.table
			{
				display: table;
				margin: 0 auto;
				height: 4vh;
			}

			.tab-content
			{
				display: none;
				height: 86vh;
			}

			.tab-content:target
			{
				display: block;
			}

			a:link
			{
				text-decoration: none;
			}

			a:visited
			{
				text-decoration: none;
			}

			a:hover
			{
				text-decoration: none;
			}

			a:active
			{
				text-decoration: none;
			}
		</style>
		<?php
			if(array_key_exists('pswd_submit', $_POST))
			{
				change_pswd();
			}
		
			if(array_key_exists('email_submit', $_POST))
			{
				change_email();
			}
		
			if(array_key_exists('logout', $_POST))
			{
				session_unset();
				session_destroy();
			}

			function change_pswd()
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
				
				if($connected && isset($_SESSION["email"]) && $_SESSION["login"] == true)
				{
					echo $_SESSION["email"];
					echo $_SESSION["login"];
					$pswd = password_hash($_POST["pswd"], PASSWORD_BCRYPT);
					
					$stmt = $conn->prepare("Update Users Set password = ? Where email = ?");
					$stmt->bind_param("ss", $pswd, $_SESSION["email"]);
					$result = $stmt->execute();
					//password change success or failure
					if($result)
					{
						echo '<script>alert("Password changed.")</script>';
					}
					if(!$result)
					{
						echo '<script>alert("Password change failed.")</script>';
					}
					$conn->close();
					return;
				}
			}
		
			function change_email()
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
				
				if($connected && isset($_SESSION["email"]) && $_SESSION["login"] == true)
				{
					$email = $_POST["email"];
					
					$stmt = $conn->prepare("Update Users Set email = ? Where email = ?");
					$stmt->bind_param("ss", $email, $_SESSION["email"]);
					$result = $stmt->execute();
					//email change success or failure
					if($result)
					{
						echo '<script>alert("Email changed.")</script>';
						$_SESSION["email"] = $email;
					}
					if(!$result)
					{
						echo '<script>alert("Email change failed.")</script>';
					}
					$conn->close();
					return;
				}
			}
		?>
	</head>
	<body>
        <div id="top">
            <button>Register/Login</button>
            <form action="search.php">
                <button>Search</button>
                <input type="text" id="search" name="search">
            </form>
        </div>
		<div>
            <form id="info" action="user.php" method="post">
                <input type="submit" name="pswd_submit" class="button" value="Password" />
                <input type="text" id="pswd" name="pswd" method="post">
                <br/>
                <input type="submit" name="email_submit" class="button" value="Email" />
                <input type="text" id="email" name="email">
				<input type="submit" name="logout" class="button" value="Log Out" />
            </form>
		</div>
	</body>
</html>
