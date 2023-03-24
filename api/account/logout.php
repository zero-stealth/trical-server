<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include './class/account.php';
include './config/database.php';

$database = new Database;
$db = $database->connect();
$account = new Account($db);



if ($account->logout()) {
    echo json_encode(
        array(
            "message" => "login successfully",
            "account ID" => "$account->id_auth",
        )
    );
} else {
    echo json_encode(
        array("message" => "logout successfull")
    );
}

