<?php 
    // https://learn.microsoft.com/en-us/sql/connect/php/sqlsrv-get-field?view=sql-server-ver16
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include '../config.php';
    $database = new database();
    $db = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'login') {
        login();
    }

    function login(){

        $name = $_GET['name'];
        echo "Hello, $name!";
        
        $password = $_GET['password'];
        echo "Password, $password!";
        
    }

    function hi(){
        echo 'hi';
    }

?>

