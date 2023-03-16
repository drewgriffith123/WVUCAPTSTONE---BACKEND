<?php 
    // https://learn.microsoft.com/en-us/sql/connect/php/sqlsrv-get-field?view=sql-server-ver16
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include '../config.php';
    $database = new database();
    $db = $database->getConnection();

    $tsql = "SELECT * FROM [dbo].[Users]";
    $stmt = sqlsrv_query($db, $tsql);
    if( $stmt === false ){  
     echo "Error in statement preparation/execution.\n";  
     die( print_r( sqlsrv_errors(), true));  
    } else {
        echo "Statement executed \n";
    }
    $array = array();
    /* Make the first row of the result set available for reading. */  
    while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC ))  {  
        // echo json_encode($row);  
        array_push($array, $row);
    }       
    echo json_encode($array);
    http_response_code(200);     
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($db);
?>

