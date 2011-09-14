<?php
//rename to db.php
mysql_connect('localhost','user','password') ||
	die(mysql_error());
mysql_select_db('database');
