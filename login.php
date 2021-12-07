<?php
session_start();
?>

<html>
	<head>
		<title></title>
		<link rel="stylesheet" href="zross/css/home.css" />
		<link rel="icon" type="image/x-icon" href="zross/favicon.ico">
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<?php
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\SMTP;
		use PHPMailer\PHPMailer\Exception;

		require 'vendor/autoload.php';
		
		if(array_key_exists('login', $_POST))
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
				login($conn);
			}
			$conn->close();
		}
		
		if(array_key_exists('change', $_POST))
		{
			$_SESSION["email"] = $_POST["pswd_change_email"];
			$email = $_POST["pswd_change_email"];
			sendMail($email, 'Change Password', "http://localhost:8000/resetpswd.php");
		}
		
		if(array_key_exists('recaptcha', $_POST))
		{
			$recaptcha = $_POST['g-recaptcha-response'];
			$res = reCaptcha($recaptcha);
			if(!$res['success']){
			  echo '<script>alert("Recaptcha failed.")</script>';
			}
			//on success allow login/register
		}
		
		function reCaptcha($recaptcha){
		  $secret = "***";
		  $ip = $_SERVER['REMOTE_ADDR'];

		  $postvars = array("secret"=>$secret, "response"=>$recaptcha, "remoteip"=>$ip);
		  $url = "https://www.google.com/recaptcha/api/siteverify";
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
		  $data = curl_exec($ch);
		  curl_close($ch);

		  return json_decode($data, true);
		}
		
		function login($conn)
		{
			$email = $_POST["email"];
			$userpswd = $_POST["pswd"];
			$login = true;

			//if email doesn't exist
			$resultRows = select("SELECT email FROM Users WHERE email = ?", $conn, $email);
			if ($resultRows[0] == null)
			{
				$login = false;
				$nonce = rand(1000,9999);
				$_SESSION["email"] = $email;
				echo $_SESSION["email"];
				$sql = "INSERT INTO Users (email, password, verify, approve, nonce) VALUES (?,?,0,0,?)";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("sss", $email, password_hash($userpswd, PASSWORD_BCRYPT), $nonce);
				$result = $stmt->execute();

				if ($result)
				{
					echo '<script>alert("Account created successfully! Please check your email for a verification link.")</script>';
					sendMail($email, 'Verify Account', "Code: $nonce http://localhost:8000/verify.php");
				}
				else
				{
					echo '<script>alert("Couldn\'t create account, please try again.")</script>';
				}
				return;
			}

			//if email does exist
			$result = select("SELECT email, password, verify, approve FROM Users WHERE email = ?", $conn, $email);
			if ($result[0] != null && $login == true)
			{
				var_dump($result[0]);
				var_dump($result[1]);
				var_dump($result[2]);
				var_dump($result[3]);

				//if email does exist but password is wrong
				$password = $result[1];
				$pswdcheck = password_verify($userpswd, $password);
				if ($pswdcheck == false)
				{
					echo '<script>alert("Wrong password. Please try again or reset password http://localhost:8000/resetpswd.php.")</script>';
					return;
				}
				
				//if not verified
				$verify = $result[2];
				if ($verify == false)
				{
					echo '<script>alert("Check email for verification or resend <here> if you didn\'t get it.")</script>';
					return;
				}

				//if verified but not approved
				$approve = $result[3];
				if ($verify == true && $approve == false)
				{
					echo '<script>alert("Not approved.")</script>';
					return;
				}

				//if verified, and approved
				//send 2fa
				if ($verify == true && $approve == true)
				{
					$nonce = rand(1000,9999);
					$_SESSION["email"] = $email; //this doesn't stick??
					$_SESSION["approved"] = true;
					echo $_SESSION["email"];
					session_write_close();
					$stmt = $conn->prepare("Update Users Set nonce = ? Where email = ?");
					$stmt->bind_param("ss", $nonce, $email);
					$result = $stmt->execute();

					sendMail($email, "Account 2FA", "Code: $nonce http://localhost:8000/verify.php");
					echo '<script>alert("Two factor authentication email has been sent.")</script>';
				}
			}
		}
		
		function select($query, $conn, $email)
		{
			$stmt = $conn->prepare($query);
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_row();
			return $row;
		}
		
		function sendMail($recipient, $subject, $content)
		{
			$mail = new PHPMailer(true);

			try {
				//Server settings
				#$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
				$mail->isSMTP();                                            //Send using SMTP
				$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
				$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
				$mail->Username   = 'zirbrainiac@gmail.com';                     //SMTP username
				$mail->Password   = '***';                               //SMTP password
				#$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
				$mail->SMTPSecure = 'tls';
				#$mail->Port       = 465;                                    //TCP port to connect to;
				$mail->Port       = 587;
				//use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

				//Recipients
				$mail->setFrom('zirbrainiac@gmail.com', 'Admin');
				$mail->addAddress("$recipient");

				//Content
				$mail->isHTML(true);                                  //Set email format to HTML
				$mail->Subject = "$subject";
				$mail->Body    = "$content";
				$mail->AltBody = "$content";

				$mail->send();
				echo 'Message has been sent';
			} catch (Exception $e) {
				echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}	
		}
		?>
	</head>
	<body>
        <div id="top">
            <button><a href="login.php">Register/Login</a></button>
			<button><a href="user.php">User Account</a></button>
            <form action="/search.php">
                <button>Search</button>
                <input type="text" id="search" name="search">
            </form>
        </div>
		<div>
            <form id="info" action="login.php" method="post">
                <label>Email</label>
                <input type="text" name="email">
                <br/>
                <label>Password</label>
                <input type="text" name="pswd">
                <input type="submit" name="login" class="button" value="Log in" />
				<label>Change Password</label>
				<input type="text" name="pswd_change_email" />
				<input type="submit" name="change" class="button" value="Change Password" />
            </form>
			<form action="?" method="POST">
			  <div class="g-recaptcha" data-sitekey="***"></div>
			  <input type="submit" name="recaptcha" value="Submit">
			</form>
		</div>
		<footer>Powered by Apache2, MySQL, and PHP 8.</footer>
	</body>
</html>
