<?php

include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM `Account` 
            WHERE `Email` = '$email' 
            AND `Password` = '$password';";

    $result = mysqli_query($con, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) {
        session_start();

        $row = mysqli_fetch_assoc($result);

        $username = $row['Username'];
        $lastviewed = $row['LastViewed'];
        
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['lastviewed'] = $lastviewed;


        echo "verified";
    } else {
        echo "false";
    }
}
?>
