<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			Dear Admin,
		</div>
		<div>
			<p>
				You have received a new feedback from user. Details are as below: <br>

                <p> Email:  {!! $email !!}</p>
                <p> Feedback Text: {!! $feedback !!} </p>
                <p> Rating: {!! $rating !!} </p>
                <p> Source: {!! $source !!} </p>

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