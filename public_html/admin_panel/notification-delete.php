<?php

ob_start();
include_once "includes/header.php";

if (isset($_GET["id"])) {
    $ID = clean($_GET["id"]);
} else {
    $ID = clean("");
}

$result = $connect->query("SELECT image FROM tbl_notification WHERE id = '$ID'");
$row = $result->fetch_assoc();
$image = $row["image"];

$delete = $connect->query("DELETE FROM tbl_notification WHERE id = '$ID'");
if ($delete) {
    $deleteImage = unlink("upload/notification/" . $image);
    $_SESSION["msg"] = "Notification deleted successfully...";
    header("Location: notification.php");
    exit();
}

include_once "includes/footer.php";

?>
