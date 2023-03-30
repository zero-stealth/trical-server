<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../../class/account.php';
include '../../config/database.php';
$database = new Database;
$db = $database->connect();
$account = new Account($db);
$stmt = $account->get_accounts();
$data_count = $stmt->rowCount();



if ($data_count > 0) {

    $account_arry = array();
    $account_arry["body"] = array();
    $account_arry["data_count"] = $data_count;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $d = array(
            'id' => $id,
            'username' => $username,
            'password' => $password,
            'registration_time' => $registration_time,
        );

         // note - session_id and password should be removed in production
        array_push($account_arry["body"], $d);
    }

    echo json_encode($account_arry);
} else {
    http_response_code(404);
    echo json_encode(
        array('message' => 'no record found, add record dummy ')
    );
}
