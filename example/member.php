<?php
session_start();
if(isset($_SESSION['loggedin']))
{
	if($_SESSION['loggedin'] == 1)
	{
			
			require_once("db.php");
			sdb_open_db("user");
			
			if(!sdb_table_exist("user"))
			{
				sdb_query("CREATE TABLE user (uid PKEY,username VARCHAR(99),password VARCHAR(99),email TEXT,gender VARCHAR(15),stid INT(99))");
			}
			
			echo "<h3>Member Area</h3><strong>Username: </strong>".$_SESSION['username']."<br/><strong>Email: </strong>".$_SESSION['email']."<br/><strong>Student ID: </strong>".$_SESSION['stid']."<br/><a href=\"logoff.php\">Logoff</a>";
			
	}
	else
	{
		header("Location: login.php");
	}
}
else
{
	header("Location: login.php");
}
?>