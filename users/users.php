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
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'getUserRoutines') {
            $user->getUserRoutines();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'assignUserRoutines') {
            $user->assignUserRoutines();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'addExercise') {
            $user->addExercise();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'removeUser') {
            $user->removeUser();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'getExercise') {
            $user->getExercise();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'getRoutines') {
            $user->getRoutines();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'removeAssignment') {
            $user->removeAssignment();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'removeRoutine') {
            $user->removeRoutine();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'createRoutine') {
            $user->createRoutine();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'roster') {
            $user->roster();
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
                $row = "User does not exist.";
            }
            echo json_encode($row);
            http_response_code(200);
        }

        function updateUserInfo(){
            // updates user info returns boolean

        }


        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=getUserRoutines
        function getUserRoutines(){
            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $tsql = "SELECT AssignmentId, RoutineId, Notes FROM [dbo].[Assignments] WHERE UserId = '$this->UserID'";
            $stmt = sqlsrv_query($this->db, $tsql);
            if( $stmt === false ){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), true));  
            }

            $rows = array();
            $i = 0;

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $i++;
                $rows[] = array('data' => $row);
            }
            if($i == 0){
                $rows = "No assigned routines at this time.";
            }
            echo json_encode($rows);
            http_response_code(200);

        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=assignUserRoutines&user=1&routine=1&notes=Rest
        function assignUserRoutines(){

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $user_ID = $_GET['user'];
            $routine_ID = $_GET['routine'];
            $notes = $_GET['notes'];
            
            $tsql = "INSERT INTO [dbo].[Assignments] values ($user_ID,$routine_ID,'$notes')";
            $stmt = sqlsrv_query($this->db, $tsql);
            if($stmt === False){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), True));  
                echo json_encode(False);
                return False;
            }
            echo json_encode(True);
            return True;
            // Assigns a user a routine
            // Evaluate Privledges

        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=addExercise&name=HamstringStretch&link=https://www.youtube.com/watch?v=T_l0AyZywjU&description=Hold
        function addExercise() {

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $name = $_GET['name'];
            $link = $_GET['link'];
            $descript = $_GET['description'];

            $check = "SELECT ExerciseId FROM [dbo].[Exercises] WHERE ExerciseName = '$name'";
            $res = sqlsrv_query($this->db, $check);
            $r = sqlsrv_fetch_array( $res, SQLSRV_FETCH_NUMERIC );
            if( $r !== NULL ){
                echo 'Exercise Already Exists.';
                echo json_encode("ID: $r[0]");
                http_response_code(409); 
                sqlsrv_free_stmt($res);
                sqlsrv_close($this->db);
                return False;
            }

            $tsql = "INSERT INTO [dbo].[Exercises] values ('$name','$link','$descript')";
            
            $stmt = sqlsrv_query($this->db, $tsql);
            if($stmt === False){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), True));  
                echo json_encode(False);
                return False;
            }
            echo json_encode(True);
            return True;
        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=removeUser&ID=2
        function removeUser(){

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $ID = $_GET['ID'];

            $tsql = "DELETE FROM [dbo].[Users] WHERE UserId = $ID";
            $stmt = sqlsrv_query($this->db, $tsql);
            if($stmt === False){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), True));  
                echo json_encode(False);
                return False;
            }

            echo json_encode(True);
            return True;

        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=getExercise&ID=2
        function getExercise(){

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $ID = $_GET['ID'];

            $tsql = "SELECT ExerciseName, Link, Descript FROM [dbo].[Exercises] WHERE ExerciseId = $ID";
            $stmt = sqlsrv_query($this->db, $tsql);
            if( $stmt === false ){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), true));  
            }
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if($row === NULL){
                $row = "Exercise does not exist.";
            }
            echo json_encode($row);
            http_response_code(200);

        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=getRoutines
        function getRoutines() {
            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $tsql = "SELECT RoutineId, RoutineName FROM [dbo].[Routines] WHERE Visible = 1 ORDER BY RoutineName";
            $stmt = sqlsrv_query($this->db, $tsql);
            if( $stmt === false ){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), true));  
            }
            $rows = array();
            $i = 0;

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $i++;
                $rows[] = array('data' => $row);
            }
            if($i == 0){
                $rows = "No routines in the system.";
            }
            echo json_encode($rows);
            http_response_code(200);
        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=removeRoutine&ID=3
        function removeRoutine() {

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $ID = $_GET['ID'];

            $tsql = "DELETE FROM [dbo].[Routines] WHERE RoutineId = $ID";
            $stmt = sqlsrv_query($this->db, $tsql);

            if($stmt === False){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), True));  
                echo json_encode(False);
                return False;
            }

            echo json_encode(True);
            return True;
        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=removeAssignment&ID=3
        function removeAssignment() {

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $ID = $_GET['ID'];

            $tsql = "DELETE FROM [dbo].[Assignments] WHERE AssignmentId = $ID";
            $stmt = sqlsrv_query($this->db, $tsql);

            if($stmt === False){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), True));  
                echo json_encode(False);
                return False;
            }

            echo json_encode(True);
            return True;
        }

        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=createRoutine&name=Legs1&IDs=4/5/6&reps=10/10/10&sets=3/4/5&visible=1
        function createRoutine() {

            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $name = $_GET['name'];
            $ID = $_GET['IDs'];
            $reps = $_GET['reps'];
            $sets = $_GET['sets'];
            $vis = $_GET['visible'];

            // Check if routine exists
            $check = "SELECT RoutineId FROM [dbo].[Routines] WHERE RoutineName = '$name'";
            $res = sqlsrv_query($this->db, $check);
            $r = sqlsrv_fetch_array( $res, SQLSRV_FETCH_NUMERIC );
            if( $r !== NULL ){
                echo 'Routine Name Exists.';
                echo json_encode("ID: $r[0]");
                http_response_code(409); 
                sqlsrv_free_stmt($res);
                sqlsrv_close($this->db);
                return False;
            }

            $tsql = "INSERT INTO [dbo].[Routines] values ('$name','$ID','$sets','$reps',$vis)";
            
            $stmt = sqlsrv_query($this->db, $tsql);
            if($stmt === False){  
                echo "Error in statement preparation/execution.\n";  
                die( print_r( sqlsrv_errors(), True));  
                echo json_encode(False);
                return False;
            }
            echo json_encode(True);
            return True;
        }


        // EXAMPLE: https://restapi-playerscompanion.azurewebsites.net/users/users.php?action=roster&name=Chase&position=WR
        function roster() {
            if(middlewareAuth($this->UserID) !== true){
                echo "Session expired. Please login again.";
                http_response_code(401);
                die();
            }

            $name = $_GET['name'];
            $pos = $_GET['position'];

            $tsql = "SELECT UserId, FirstName, LastName, PlayerNumber, Position FROM [dbo].[Users] WHERE UserType = 'P'";

            if ($pos != "") {
                if ($pos == "O") {
                    $tsql .= " AND (Position = 'C' OR Position = 'OG' OR Position = 'OT' OR Position = 'RB' OR Position = 'QB' OR Position = 'WR' OR Position = 'TE')";
                }
                else if ($pos == "D") {
                    $tsql .= " AND (Position = 'DT' OR Position = 'DE' OR Position = 'MLB' OR Position = 'OLB' OR Position = 'CB' OR Position = 'S')";
                }
                else if ($pos == "ST") {
                    $tsql .= " AND (Position = 'P' OR Position = 'K' OR Position = 'H' OR Position = 'LS' OR Position = 'KR' OR Position = 'PR')";
                }
                else{
                    $tsql .= " AND Position = '$pos'";
                }
            }
            if ($name != "") {
                if (strpos($name," ") != false) {
                    strtolower($name);
                    $names = explode(" ",$name);
                    $tsql .= " AND (lower(FirstName) like '$names[0]%' AND lower(LastName) like '$names[1]%')";
                }
                else {
                    strtolower($name);
                    $tsql .= " AND (lower(FirstName) like '$name%' OR lower(LastName) like '$name%')";
                }
            }
            
            $stmt = sqlsrv_query($this->db, $tsql);

            $rows = array();
            $i = 0;

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $i++;
                $rows[] = array('data' => $row);
            }
            if($i == 0){
                $rows = "";
            }

            echo json_encode($rows);
            http_response_code(200);
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

