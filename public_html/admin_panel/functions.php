<?php

function insert($table, $data)
{
    include "includes/config.php";
    $fields = array_keys($data);
    $values = array_map([$connect, "real_escape_string"], array_values($data));

    $sql =
        "INSERT INTO $table (" .
        implode(",", $fields) .
        ") VALUES ('" .
        implode("','", $values) .
        "')";
    mysqli_query($connect, $sql);
}

function delete($table_name, $where_clause = "")
{
    include "includes/config.php";
    $whereSQL = "";
    if (!empty($where_clause)) {
        if (substr(strtoupper(trim($where_clause)), 0, 5) != "WHERE") {
            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }
    $sql = "DELETE FROM " . $table_name . $whereSQL;
    return mysqli_query($connect, $sql);
}

function update($table_name, $form_data, $where_clause = "")
{
    include "includes/config.php";
    $whereSQL = "";
    if (!empty($where_clause)) {
        if (substr(strtoupper(trim($where_clause)), 0, 5) != "WHERE") {
            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }
    $sql = "UPDATE " . $table_name . " SET ";
    $sets = [];
    foreach ($form_data as $column => $value) {
        $sets[] = "`" . $column . "` = '" . $value . "'";
    }
    $sql .= implode(", ", $sets);
    $sql .= $whereSQL;
    return mysqli_query($connect, $sql);
}

function clean($data)
{
    include "includes/config.php";
    $data = mysqli_real_escape_string($connect, $data);
    return $data;
}

function encrypt($data)
{
    $data = base64_encode(base64_encode(base64_encode($data)));
    return $data;
}

function decrypt($data)
{
    $data = base64_decode(base64_decode(base64_decode($data)));
    return $data;
}

function FCM(
    $uniqueId,
    $title,
    $message,
    $bigImage,
    $link,
    $postId,
    $fcmServerKey,
    $fcmNotificationTopic,
    $redirect
) {
    $data = [
        "to" => "/topics/" . $fcmNotificationTopic,
        "data" => [
            "title" => $title,
            "message" => $message,
            "big_image" => $bigImage,
            "link" => $link,
            "post_id" => $postId,
            "unique_id" => $uniqueId,
        ],
    ];

    $header = [
        "Authorization: key=" . $fcmServerKey,
        "Content-Type: application/json",
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(false);
    } else {
        echo json_encode(true);
    }

    curl_close($ch);

    $_SESSION["msg"] = "FCM push notification sent...";
    header($redirect);
    exit();
}

function ONESIGNAL(
    $uniqueId,
    $title,
    $message,
    $bigImage,
    $link,
    $postId,
    $oneSignalAppId,
    $oneSignalRestApiKey,
    $redirect
) {
    $content = ["en" => $message];

    $fields = [
        "app_id" => $oneSignalAppId,
        "included_segments" => ["All"],
        "data" => [
            "foo" => "bar",
            "link" => $link,
            "post_id" => $postId,
            "unique_id" => $uniqueId,
        ],
        "headings" => ["en" => $title],
        "contents" => $content,
        "big_picture" => $bigImage,
        "url" => $link,
    ];

    $fields = json_encode($fields);
    print "\nJSON sent:\n";
    print $fields;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json; charset=utf-8",
        "Authorization: Basic " . $oneSignalRestApiKey,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $_SESSION["msg"] = "OneSignal push notification sent...";
    header($redirect);
    exit();
}

function generateApiKey($chars = 45)
{
    $characters =
        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($characters), 0, $chars);
}

function pagination($reload, $page, $keyword, $tpages)
{
    $prevlabelDisabled =
        "<div class='padding-pagination button-grey button-rounded'><span aria-hidden='true'>&larr;</span>&nbsp;&nbsp;Previous</div>";
    $prevlabel =
        "<div class='padding-pagination button button-rounded waves-effect waves-float'><span aria-hidden='true'>&larr;</span>&nbsp;&nbsp;Previous</div>";

    $nextlabel =
        "<div class='padding-pagination button button-rounded waves-effect waves-float'>Next&nbsp;&nbsp;<span aria-hidden='true'>&rarr;</span></div>";
    $nextlabelDisabled =
        "<div class='padding-pagination button-grey button-rounded'>Next&nbsp;&nbsp;<span aria-hidden='true'>&rarr;</span></div>";

    $current = "<div class='padding-pagination'>Page $page of $tpages</div>";

    $out = "<ul class='pager'>";

    if ($page == 1) {
        $out .=
            "<li class='previous disabled'><a>" .
            $prevlabelDisabled .
            "</a></li>";
    } elseif ($page == 2) {
        $out .=
            "<li class='previous'><a href='" .
            $reload .
            "'>" .
            $prevlabel .
            "</a></li>";
    } else {
        $out .=
            "<li class='previous'><a href='" .
            $reload .
            "?page=" .
            ($page - 1) .
            "&keyword=" .
            $keyword .
            "'>" .
            $prevlabel .
            "</a></li>";
    }

    $out .= "<li><a>" . $current . "</a></li>";

    if ($page < $tpages) {
        $out .=
            "<li class='next'><a href='" .
            $reload .
            "?page=" .
            ($page + 1) .
            "&keyword=" .
            $keyword .
            " '>" .
            $nextlabel .
            "</a></li>";
    } else {
        $out .=
            "<li class='next disabled'><a>" . $nextlabelDisabled . "</a></li>";
    }

    $out .= "</ul>";

    return $out;
}

function remotefileSize($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_exec($ch);
    $filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);
    if ($filesize) {
        return $filesize;
    }
}

function reArrayFiles(&$file_post)
{
    $file_ary = [];
    $file_count = count((array) $file_post["name"]);
    $file_keys = array_keys((array) $file_post);
    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

function jsonEncode($token, $serverUrl, $applicationId)
{
    $accessKey =
        $token . base64_encode($serverUrl . "_applicationId_" . $applicationId);
    return $accessKey;
}

function jsonDecode($token, $accessKey)
{
    $data = str_replace($token, "", $accessKey);
    $decode = base64_decode($data);
    return $decode;
}

?>