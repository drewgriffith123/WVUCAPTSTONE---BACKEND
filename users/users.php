<?php 
    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    // header("Access-Control-Allow-Methods: POST");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include '../config.php';
    $database = new database();
    echo 'hi';
    $db = $database->getConnection();
    echo 'hey';
    $sql = "SELECT * FROM [dbo].[Users]";
            // $expr = sqlsrv_query($conn, $sql);
    sqlsrv_query($db, $sql);
    http_response_code(200);     
    echo json_encode($db);
?>

