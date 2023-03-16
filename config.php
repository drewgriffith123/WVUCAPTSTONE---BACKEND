<?php
    // Have to get the connection object from server to query in other classes
    class database{
        
        public function getConnection(){
            try {
                $conn = new PDO("sqlsrv:server = tcp:wvurms.database.windows.net,1433; Database = RMS APP", "azureuser", "WVUrms12");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            }catch (PDOException $e) {
                echo "Error connecting to SQL Server.";
                die(print_r($e));
            }
        
            // SQL Server Extension Sample Code:
            $connectionInfo = array("UID" => "azureuser", "pwd" => "WVUrms12", "Database" => "RMS APP", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
            $serverName = "tcp:wvurms.database.windows.net,1433";
            $conn = sqlsrv_connect($serverName, $connectionInfo);
            
            return $conn;

            // $testSQL = "SELECT UserId, FirstName, LastName, PlayerNumber FROM [dbo].[Users] WHERE UserType = \'P\'";
            // $expr = sqlsrv_query($conn, $sql);
        }
        
    }

?>
  