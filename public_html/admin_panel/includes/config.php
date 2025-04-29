<?php

//database configuration
$host = "localhost";
$username = "earntbpk_app";
$password = "thevantom";
$database = "earntbpk_app";

$connect = new mysqli($host, $username, $password, $database);

if (mysqli_connect_errno()) {
    die("Whoops! failed to connect to database : " . mysqli_connect_error());
} else {
    $connect->set_charset("utf8mb4");
}

$ENABLE_RTL_MODE = "false";

?>