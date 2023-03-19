<?php 
    // Every request to this URI must have the session_token
    // Sample token: 8fdd7ed66955cc8c6fc0f5dd0f294017779ce12536311d960f032ce90c7d6902
    // Gets all user info if authorized.
    /*
    !IMPORTANT!
    You must include the users session token in the Authroization header of the request. Or else nothing will be returned.
    The code will look something like this in react-native
    
    fetch('https://example.com/profile', {
    headers: {
        'Authorization': 'Bearer ' + sessionCookie
    }
    })
    .then(response => {
        // handle response
    })
        .catch(error => {
        // handle error
    });
    */
    // https://learn.microsoft.com/en-us/sql/connect/php
    //
    // ----------------------------------------------------------------------------------------------------------------------------------

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // this is also including ../config.php
    include './auth.php';
    if (strpos($_SERVER['REQUEST_URI'], '/users/users.php') !== False) {
        $database = new database();
        $db = $database->getConnection();
        
        // Get session_token
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
                $token = $matches[1];
                
                // token is now available for further processing
                // e.g. validate token, decode token, etc.
                // Initialize user
                $user = new user($token);

            } else {
                // header format is invalid
                http_response_code(400);
                echo 'Invalid Authorization header format.';
                die();
            }
        }else{
            echo "Missing Authorization Header.";
            http_response_code(401);
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'userinfo') {
            $user->getUserInfo();
        }else{
            echo "Specified action not available.";
            http_response_code(405);
            // die();
        }
    }

    class user{
        private $session_token;
        private $type; // Four types {T, P, AT, G} | Trainer, Player, AdminTrainer, Grant (lol)
        public $UserID;
        private $db;

        function __construct($session_token){
            $this->session_token = $session_token;
            // $this->type = $type;
            // $this->UserID = $UserID;
            $database = new database();
            $this->db = $database->getConnection();

            $tsql = "SELECT UserID FROM [dbo].[Sessions] WHERE SessionToken = '$session_token'";
            $stmt = sqlsrv_query($this->db, $tsql);
            if( $stmt === false ){  
             echo "Error in statement preparation/execution.\n";  
             die( print_r( sqlsrv_errors(), true));  
            }

            $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC );
            if($row === NULL){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $this->UserID = $row[0];
            // Get User Type
            $userTypeSQLStatement = "SELECT UserType FROM [dbo].[Users] WHERE UserId = $this->UserID";
            $statement = sqlsrv_query($this->db, $userTypeSQLStatement);
            if( $statement === false ){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), true));  
            }
            $r = sqlsrv_fetch_array( $statement, SQLSRV_FETCH_NUMERIC );
            $this->type = $r[0];
        }

        function getUserInfo(){
            // returns user info
            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $tsql = "SELECT * FROM [dbo].[Users] WHERE UserId = '$this->UserID'";
            $stmt = sqlsrv_query($this->db, $tsql);
            if( $stmt === false ){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), true));  
            }
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if($row === NULL){
                echo "User does not exist.";
            }
            echo json_encode($row);
            http_response_code(200);
        }

        function updateUserInfo(){
            // updates user info returns boolean

        }

        function getUserRoutines(){
            // returns all routines assigned to specific user

        }

        function assignUserRoutines(){
            // Assigns a user a routine
            // Evaluate Privledges

        }

        function getUserNotes(){
            // Gets a users notes
    
        }

        function postUserNotes(){
            // Adds notes to user

        }

        function getLogsID(){
            // returns ID number of log needed

        }

    }


    // $array = array();
    /* Make the first row of the result set available for reading. */  
    // while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC ))  {  
    //     // echo json_encode($row);  
    //     array_push($array, $row);
    // }       

    function getAllUsers($session_token, $limit){
        // Returns all the users up to a certain numeraical limit.
        // Can only perform this action if AT.

    }
    
    // http_response_code(200);     
    
?>

