<?php require('../../inc/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Simple DataBase Documention</title>
<link href="sdb_style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="header">
	<span class="main_menu">SDB (<span class="header_style">S</span>imple <span class="header_style">D</span>ata-<span class="header_style">B</span>ase) Manual</span>
    <span style="margin-left:25px;"><a href="sdb_backup.html">sdb_backup &raquo;</a></span>
</div>

<div id="main">

	<h3>Getting Started with SDB API</h3>
    <p>SDB is a database system which is 100% coded in PHP. In other words SDB is a storage device which basically store data, print data, delete data... SDB is an alias of the big databases available today in the market like MySQL, MsSQL, PostgreSQL. SDB is generally created for education purposes. I still prefer to use MySQL or PostgreSQL if you are running production website. SDB use clean SQL Query language and inspired by MySQl so most of the functions are similar to MySQL. For smaller or basic PHP script I prefer to use SDB API because of the low memory usage and faster speed, and great profit is you do not need to have any database installed on your server, so if you don't have root access to the server and does not have database open SDB API is best to idea if you want to use database.</p>
	<ul>
    	<li>Function List
        	<ul>
            	<li><a href="sdb_backup.html">sdb_backup</a></li>
                <li><a href="sdb_close_db.html">sdb_close_db</a></li>
                <li><a href="sdb_columns_exist.html">sdb_column_exist</a></li>
                <li><a href="sdb_count_table_column.html">sdb_count_table_column</a></li>
                <li><a href="sdb_db_exist.html">sdb_db_exist</a></li>
                <li><a href="sdb_db_size.html">sdb_db_size</a></li>
                <li><a href="sdb_drop_table.html">sdb_drop_table</a></li>
                <li><a href="sdb_get_column_place.html">sdb_get_column_place</a></li>
                <li><a href="sdb_get_table_column.html">sdb_get_table_column</a></li>
                <li><a href="sdb_get_table_value.html">sdb_get_column_value</a></li>
                <li><a href="sdb_init.html">sdb_init</a>
                <li><a href="sdb_num_rows.html">sdb_num_rows</a>
                <li><a href="sdb_open_db.html">sdb_open_db</a></li>
                <li><a href="sdb_query.html">sdb_query</a></li>
                <li><a href="sdb_real_escape_string.html">sdb_real_escape_string</a></li>
                <li><a href="sdb_replace_keywords.html">sdb_replace_keywords</a></li>
                <li><a href="sdb_table_exist.html">sdb_table_exist</a></li>
            </ul>
        </li>        
        <li>Examples
        	<ul>
            	<li><a href="ex1.html">Example 1: Creating database, Creating table and check if Database or Table exists</a></li>
                <li><a href="ex2.html">Example 2: Inserting datas, Selecting datas</a></li>
                <li><a href="ex3.html">Example 3: Checking for Malicious Data and escape from those</a></li>
                <li><a href="ex4.html">Example 4: Creating Usermembership with SDB API</a></li>
            </ul>
        </li>
    </ul>
<hr />
Copyright &copy; Sidd Panchal
</div>



</body>
</html>
<?php putSeeAlso_Chapter1(); ?>
<?php require('../../inc/footer.php'); ?>