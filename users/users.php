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
    $res = sqlsrv_query($db, $sql);
    echo $res;
    if ($err = sqlsrv_errors()) {
        echo "There were errors or warnings!<br/>";
        print_r($err);
        echo "<br/>";
    }
    print_r($res);
    print_r(json_encode($res));
    echo json_encode($res);
    http_response_code(200);     
?>

