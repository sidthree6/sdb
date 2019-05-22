<?php
session_start();
require_once("db.php");

//sdb_init();
sdb_open_db("user");
//sdb_query("CREATE TABLE sdb_test (id PKEY,name VARCHAR(99))");
//sdb_table_exist("user");
$check = sdb_query("SELEhkCT * FROM user WHERE username='siddhu'");

echo sdb_num_rows($check);
/*
if(!sdb_table_exist("user"))
{
		sdb_query("CREATE TABLE user (id PKEY,username VARCHAR(255),password VARCHAR(255),ip VARCHAR(255))");
}

if(isset($_POST['submit']))
{
		$username = sdb_replace_keywords(sdb_real_escape_string($_POST['username']));
		$password = sdb_replace_keywords(sdb_real_escape_string($_POST['password']));
		if(empty($username) || empty($password))
		{
				echo "You must fill all the fields <a href='index.php'>&laquo; Go Back</a>";
		}
		else
		{
				sdb_query("INSERT INTO user (username,password) VALUES ('".$username."','".$password."')");
				echo "You are successfully registered now you can login, please <a href='index.php'>go back and login</a>";
		}
}
elseif(isset($_POST['login']))
{
		$username = sdb_replace_keywords(sdb_real_escape_string($_POST['username']));
		$password = sdb_replace_keywords(sdb_real_escape_string($_POST['password']));
		if(empty($username) || empty($password))
		{
				echo "You must fill all the fields <a href='index.php'>&laquo; Go Back</a>";
		}
		else
		{
				$check = sdb_query("SELECT * FROM user WHERE username='".$username."' AND password='".$password."'");
				if(sdb_num_rows($check) == 0)
				{
						echo "Username or Password does not match, please <a href='index.php'>go back and login</a>";
				
				}
				else
				{
						echo "Welcome back, ".$username."<br/>";
				}
		}
}
else
{
?>

<fieldset style="background-color:#F0F0F0">
<legend>Create An Account</legend>
<form action="index.php" method="post">
Username: <input type="text" name="username" />
Password: <input type="text" name="password" />
<input type="submit" name="submit" value="Create an Account" />
</form>
</fieldset>

<fieldset style="background-color:#F0F0F0">
<legend>Login</legend>
<form action="index.php" method="post">
Username: <input type="text" name="username" />
Password: <input type="text" name="password" />
<input type="submit" name="login" value="login" />
</form>
</fieldset>
<?php
}
*/
//sdb_close_db();

?>


