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
$account_data = json_decode(file_get_contents('php://input'));

$account->username = $account_data->username;
$account->password = $account_data->password;

try {

if ($account->create_account()) {
 echo json_encode(
        array(
            "message" => "account created ",
            "account name" => "$account->username",
        )
    );
} else {
    echo json_encode(
        array("message" => "account created",
        "account_name" => "$account->username",
        )

    );
}

} catch(Exception $e) {
    echo $e->getMessage();
    die();

}