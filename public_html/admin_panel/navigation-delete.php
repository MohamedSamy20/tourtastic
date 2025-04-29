<?php 

ob_start();
include_once('includes/header.php');

if (isset($_GET["id"])) {
    $ID = clean($_GET["id"]);
} else {
    $ID = clean("");
}

$result = $connect->query("SELECT image FROM tbl_menu WHERE id = '$ID'");
$row = $result->fetch_assoc();
$image = $row["image"];

$delete = $connect->query("DELETE FROM tbl_menu WHERE id = '$ID'");

if ($delete) {
    $deleteImage = unlink("upload/" . $image);
    $_SESSION["msg"] = "Menu deleted successfully...";
    header("Location: navigation.php");
    exit();
}

include_once('includes/footer.php');

?>