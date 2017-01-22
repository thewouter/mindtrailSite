<?php
// Establishing Connection with Server by passing server_name, user_id and password as a parameter
// $connection = mysql_connect("localhost", "mindtrail", "mindtrailpw");
// Selecting Database
// $db = mysql_select_db("mindtrail", $connection);
session_start();// Starting Session
// Storing Session
$username=$_SESSION['login_user'];
// SQL Query To Fetch Complete Information Of User
// $ses_sql=mysql_query("select username from login where username='$user_check'", $connection) or die(mysql_error());
// $row = mysql_fetch_assoc($ses_sql);
// $login_session =$row['username'];

$db = new PDO('mysql:host=localhost;dbname=mindtrail;charset=utf8mb4', 'mindtrail', 'mindtrailpw');
// SQL query to fetch information of registerd users and finds user match.
$login_session = $db->query("select * from login where username='$username'")->fetch(PDO::FETCH_ASSOC);

if(!isset($login_session)){
	header('Location: index.php'); // Redirecting To Home Page
}
?>
