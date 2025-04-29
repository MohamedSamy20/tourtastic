<?php

ob_start();
include_once "includes/header.php";

if (isset($_GET["id"])) {
    $ID = clean($_GET["id"]);
} else {
    $ID = clean("");
}

$delete = $connect->query("DELETE FROM tbl_admin WHERE id = '$ID'");
if ($delete) {
    $_SESSION["msg"] = "Admin deleted successfully...";
    header("Location: admin.php");
    exit();
}

include_once "includes/footer.php";

?>