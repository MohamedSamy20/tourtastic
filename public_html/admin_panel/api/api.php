<?php

require_once("../includes/config.php");
require_once("functions.php");

if (isset($_GET["application_id"])) {
    $applicationIdReceived = $_GET["application_id"];
    $result = $connect->query(
        "SELECT * FROM tbl_app_config WHERE application_id LIKE N'%$applicationIdReceived%' "
    );
    $row = mysqli_fetch_assoc($result);
    $applicationId = $row["application_id"];

    if ($applicationIdReceived == $applicationId) {
        $menus = getListResult(
            $connect,
            "SELECT * FROM tbl_menu ORDER BY id ASC"
        );
        $app = getOneResult(
            $connect,
            "SELECT * FROM tbl_app_config WHERE application_id = '$applicationId'"
        );
        $ads = getOneResult($connect, "SELECT * FROM tbl_ads LIMIT 1");
        $adsPlacement = getOneResult(
            $connect,
            "SELECT * FROM tbl_ads_placement LIMIT 1"
        );
        $settings = getOneResult(
            $connect,
            "SELECT * FROM tbl_settings LIMIT 1"
        );

        $response = [
            "status" => "ok",
            "app" => $app,
            "menus" => $menus,
            "ads" => $ads,
            "ads_placement" => $adsPlacement,
            "settings" => $settings,
        ];
        response($response);
    } else {
        responseInvalidParam("Forbidden access!");
    }
} else {
    responseInvalidParam("No method found!");
}

?>