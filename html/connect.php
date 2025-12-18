<?php
//Makes DB connection
$servername = "sql1.njit.edu";
$username = "ltl2";
$password = "sqlPassword!1";
$dbname = "ltl2";
$con = mysqli_connect($servername,$username,$password,$dbname);
if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>