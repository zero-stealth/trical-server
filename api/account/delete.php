<?php 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();
include_once './class/account.php';
include_once './config/database.php';

$database = new Database;
$db = $database->connect();
$account = new Account($db);
$account_data = json_decode(file_get_contents('php://input'));

$account->id = $account_data->id;

if($account->delete_account()) {
    echo json_encode(
        array("message" => "Account deleted successfully"));

} else {
    echo json_encode(
        array("message" => "Account was not deleted dummy"));
}

