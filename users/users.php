<?php 
    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    // header("Access-Control-Allow-Methods: POST");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include '../config.php';
    $database = new database();
    $db = $database->getConnection();

    $sql = "SELECT * FROM [dbo].[Users]";
    $stmt = sqlsrv_query($db, $sql);
    if( $stmt === false ){  
     echo "Error in statement preparation/execution.\n";  
     die( print_r( sqlsrv_errors(), true));  
    } 

    /* Make the first row of the result set available for reading. */  
    while($res = sqlsrv_fetch_row( $stmt ))  {  
     echo res;  
    }       


    // $name = sqlsrv_get_field( $stmt, 0);  
    // echo "$name: ";  
    // echo $res;

    print_r(json_encode($stmt));
    echo json_encode($stmt);
    http_response_code(200);     
    sqlsrv_free_stmt($stmt);
?>

