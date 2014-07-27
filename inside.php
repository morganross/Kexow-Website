<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>Kexow</title>
<link href='default.css' rel='stylesheet' type='text/css' />
<script src='models/funcs.js' type='text/javascript'>
</script>

</head>";

echo "
<body>
<div id='left-nav'>";

//Links for logged in user
if(isUserLoggedIn()) {
	echo "
	<ul>

	<li><a target='_top' <a href='connect.html'>";
echo " $loggedInUser->displayname  connect now</a></li>
	<li><a href='user_settings.php'>User Settings</a></li>

	
</ul>";

echo " <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">
	<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
		<input type=\"hidden\" name=\"hosted_button_id\" value=\"ZHZDMRG58XZH6\">
		<input type=\"hidden\" name=\"on0\" value=\"insertvar2\">
		<input type=\"hidden\" name=\"os0\" value=\"";
echo " $loggedInUser->username\">
		<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
		<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">
		</form>";
echo " <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">
	<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
		<input type=\"hidden\" name=\"hosted_button_id\" value=\"86GVDX9KZTSYS\">
		<input type=\"hidden\" name=\"on0\" value=\"insertvar1\">
		<input type=\"hidden\" name=\"os0\" value=\"";
echo " $loggedInUser->username\">
		<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
		<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">
		</form>";		
} 
//Links for users not logged in
else {
	echo "
	<ul>
	<li><a href='register.php'>Register</a></li>
	<li><a href='forgot-password.php'>Forgot Password</a></li>";
	
//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = sanitize(trim($_POST["username"]));
	$password = trim($_POST["password"]);
	
	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
	}
	if($password == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}

	if(count($errors) == 0)
	{
		//A security note here, never tell the user which credential was incorrect
		if(!usernameExists($username))
		{
			$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
		}
		else
		{
			$userdetails = fetchUserDetails($username);
			//See if the user's account is activated
			if($userdetails["active"]==0)
			{
				$errors[] = lang("ACCOUNT_INACTIVE");
			}
			else
			{
				//Hash the password and use the salt from the database to compare the password.
				$entered_pass = generateHash($password,$userdetails["password"]);
				
				if($entered_pass != $userdetails["password"])
				{
					//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
					$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
				}
				else
				{
					//Passwords match! we're good to go'
					
					//Construct a new logged in user object
					//Transfer some db data to the session object
					$loggedInUser = new loggedInUser();
					$loggedInUser->email = $userdetails["email"];
					$loggedInUser->user_id = $userdetails["id"];
					$loggedInUser->hash_pw = $userdetails["password"];
					$loggedInUser->title = $userdetails["title"];
					$loggedInUser->displayname = $userdetails["display_name"];
					$loggedInUser->username = $userdetails["user_name"];
					
					//Update last sign in
					$loggedInUser->updateLastSignIn();
					$_SESSION["userCakeUser"] = $loggedInUser;
					
					//Redirect to user account page
					header("Location: inside.php");
					die();
				}
			}
		}
	}
}



echo "
<body>




<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<div id='regbox'>
<form name='login' action='".$_SERVER['PHP_SELF']."' method='post'>
<p>
<label>Username:</label>
<input type='text' name='username' />
</p>
<p>
<label>Password:</label>
<input type='password' name='password' />
</p>
<p>
<label>&nbsp;</label>
<input type='submit' value='Login' class='submit' />
</p>
</form>

</div>

</div>
</body>
</html>";
}






echo "


</div>
</body>
</html>";

?>
