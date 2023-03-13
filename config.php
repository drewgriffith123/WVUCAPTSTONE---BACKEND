<?php



try {
    $serverName = "wvurms.database.windows.net";
    $databaseName = "RMS APP";
    $uid = "azureuser";
    $pwd = "WVUrms12";
    
    $conn = new PDO("sqlsrv:server = $serverName; Database = $databaseName;", $uid, $pwd);

    // Select Query
    $tsql = "SELECT @@Version AS SQL_VERSION";

    // Executes the query
    $stmt = $conn->query($tsql);
    echo $stmt;
} catch (PDOException $exception1) {
    echo "<h1>Caught PDO exception:</h1>";
    echo "<h1>PHP Info for troubleshooting</h1>";
}
    
?>    