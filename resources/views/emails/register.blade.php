<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			Dear User,
		</div>
		<div>
			<p>
				Thank You For Register in Redeemar.Please check your username and password as below: <br>

                <p>Username:  {!! $user_email !!}</p>
                <p> Password: {!! $registerpassword !!} </p>
                <p>&nbsp;</p>
				<p>Please click here to login <a href="http://159.203.91.38/admin/public/index.php/auth/login" target="_blank">Login</a>.
			</p>
		</div>
		<br/>
		<div>Sincerely,<br/>
			Redeemar Team
		</div>
	</body>
</html>