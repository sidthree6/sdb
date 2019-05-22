<?php
session_start();
require_once("db.php");
sdb_open_db("user");

if(!sdb_table_exist("user"))
{
    sdb_query("CREATE TABLE user (uid PKEY,username VARCHAR(99),password VARCHAR(99),email TEXT,gender VARCHAR(15),stid INT(99))");
} 

if(isset($_POST['login']))
{
	$username = sdb_real_escape_string(sdb_replace_keywords($_POST['username']));
	$password = sdb_real_escape_string(sdb_replace_keywords($_POST['password']));
	
	if(empty($username) || empty($password))
	{
			echo "You must fields are required <br/>";
	}
	else
	{
			$check_login = sdb_query("SELECT * FROM user WHERE username='".$username."' AND password='".$password."'");
			if(sdb_num_rows($check_login) == 1)
			{
					$_SESSION['username'] = $check_login['username'][0];
					$_SESSION['email'] = $check_login['email'][0];
					$_SESSION['stid'] = $check_login['stid'][0];
					$_SESSION['loggedin'] = 1;
					header("Location: member.php");
			}
			else
			{
					
					echo "Invalid Username or password, please try again<br/>";
			}
	}
}

?>
<form action="login.php" method="post">
<table>
<tr><td>Username: </td><td><input type="text" name="username" /></td></tr>
<tr><td>Password: </td><td><input type="password" name="password" /></td></tr>
<tr><td></td><td><input type="submit" value="Login" name="login" /></td></tr>
</table>
</form>
<a href='register.php'>Create an Account</a>