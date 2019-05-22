<?php
session_start();
require_once("db.php");
sdb_open_db("user");

if(!sdb_table_exist("user"))
{
    sdb_query("CREATE TABLE user (uid PKEY,username VARCHAR(99),password VARCHAR(99),email TEXT,gender VARCHAR(15),stid INT(99))");
} 

if(isset($_POST['register']))
{
	$username = sdb_real_escape_string(sdb_replace_keywords($_POST['username']));
	$password = sdb_real_escape_string(sdb_replace_keywords($_POST['password']));
	$email    = sdb_real_escape_string(sdb_replace_keywords($_POST['email']));
	$gender   = sdb_real_escape_string(sdb_replace_keywords($_POST['gender']));
	$stid     = sdb_real_escape_string(sdb_replace_keywords($_POST['stid']));
	
	if(empty($username) || empty($password) || empty($email) || empty($gender) || empty($stid))
	{
			echo "You must fields are required <br/>";
	}
	else
	{
			$check_username = sdb_query("SELECT * FROM user WHERE username='".$username."'");
			if(sdb_num_rows($check_username) > 0)
			{
					echo "Username is already taken <br/>";
			}
			else
			{
					sdb_query("INSERT INTO user (username,password,email,gender,stid) VALUES ('".$username."','".$password."','".$email."','".$gender."','".$stid."')");
					echo "You are successfully registered, Now you can <a href=\"login.php\">Login</a>";
			}
	}
}

?>
<form action="register.php" method="post">
<table>
<tr><td>Username: </td><td><input type="text" name="username" /></td></tr>
<tr><td>Password: </td><td><input type="password" name="password" /></td></tr>
<tr><td>Email: </td><td><input type="text" name="email" /></td></tr>
<tr><td>Gender: </td><td> Male<input type="radio" name="gender" value="male" checked /> Female<input type="radio" name="gender" value="female" /></td></tr>
<tr><td>Student ID: </td><td><input type="text" name="stid" /></td></tr>
<tr><td></td><td><input type="submit" value="Register" name="register" /></td></tr>
</table>
</form>
<a href='login.php'>Login from here</a>