<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST");
    include '../config.php';
    $database = new database();
    $db = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'login') {
        login();
    }

    // get request to login evaluates the username and password credientials
    // returns session ID as cookie
    // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/auth.php?action=login&name=john&password=smith
    function login(){
        $name = $_GET['name'];
        $password = $_GET['password'];

        $tsql = "SELECT Username, Password FROM [dbo].[Users] WHERE Username = \'$name\'";
        $stmt = sqlsrv_query($db, $tsql);
        if( $stmt === false ){  
            echo "Error in statement preparation/execution.\n";  
            die( print_r( sqlsrv_errors(), true));  
        }

        $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC );
        echo json_encode($row);

    }

    // before any request/post is requred we run the middleware auth to evaluate
    // session cookie token is active
    function middlewareAuth(){
        echo 'hi';
    }

?>

