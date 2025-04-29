<?php

function getListResult($connect, $query)
{
    $rows = [];
    $result = $connect->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function getOneResult($connect, $query)
{
    $rows = [];
    $result = $connect->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $rows = $row;
        }
    }
    return $rows;
}

function response($array)
{
    header("Content-Type: application/json; charset=utf-8");
    echo $val = str_replace(
        "\\/",
        "/",
        json_encode($array, JSON_NUMERIC_CHECK)
    );
}

function responseInvalidParam($message)
{
    $response = ["status" => "error", "message" => $message];
    header("Content-Type: application/json; charset=utf-8");
    echo $val = str_replace(
        "\\/",
        "/",
        json_encode($response, JSON_NUMERIC_CHECK)
    );
}

?>
