<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

include './class/account.php';
include './config/database.php';

$database = new Database;
$db = $database->connect();
$account = new Account($db);
$account->id =isset($_GET['id']) ? $_GET['id'] : exit(); //getting param id


$account->get_account();
if($account->username != null) {
    //create array
    $account_arry = array(
        "id"=> $account-> id,
        "username"=> $account-> username,
        "password"=> $account->password,
        "registration_time"=> $account->registration_time,
        "account_active"=> $account->account_active,  
    );

    http_response_code(200);
    echo json_encode($account_arry);

} else {
    http_response_code(404);
    echo json_encode(
        array('message'=> 'account not found, maybe you should enroll one')
    );

}
