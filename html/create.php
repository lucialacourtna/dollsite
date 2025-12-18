<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM `Account` 
            WHERE `Email` = '$email' 
            OR `Username` = '$username';";

    $result = mysqli_query($con, $sql) or die("Query failed: " . mysqli_error($con));


    if (mysqli_num_rows($result) === 0) {
        session_start();

        $sql = "INSERT INTO `Account`(`Username`, `Email`, `Password`, `LastViewed`) VALUES ('$username','$email','$password','0')";

        $result = mysqli_query($con, $sql) or die("Query failed: " . mysqli_error($con));
        if ($result){
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['lastviewed'] = 0;
        echo "verified";

        }

        else{
            echo "Error adding to database";
        }
    } else {
        echo "false";
    }
}
?>
