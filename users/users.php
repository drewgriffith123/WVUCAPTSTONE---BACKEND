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
    $numRows = sqlsrv_num_rows($stmt);
   
    echo "Number of rows: $numRows \n";

    if( sqlsrv_fetch( $stmt ) === false){  
         echo "Error in retrieving row.\n";  
         die( print_r( sqlsrv_errors(), true));  
    }  

    $name = sqlsrv_get_field( $stmt, 0);  
    echo "$name: ";  
    

    
    /* Make the first row of the result set available for reading. */  
    // while($row = sqlsrv_fetch_row( $stmt, SQLSRV_FETCH_NUMERIC ))  {  
    //     echo "UserID: ".$row[0]."\n";  
    //     echo "FirstName: ".$row[1]."\n";  
    //     echo "MiddleName: ".$row[2]."\n";  
    //     echo "LastName: ".$row[3]."\n"; 
    //     echo "UserType: ".$row[4]."\n"; 
    //     echo "UserName: ".$row[5]."\n"; 
    //     echo "Password: ".$row[6]."\n"; 
    //     echo json_encode($row);  
    // }       


    // $name = sqlsrv_get_field( $stmt, 0);  
    // echo "$name: ";  
    // echo $res;

    echo json_encode($stmt);
    http_response_code(200);     
    sqlsrv_free_stmt($stmt);
?>

