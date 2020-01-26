<?php
require("./sql_config.php");
require("./Re_josn.php");

// Returns all the names in the database and their latitude and longitude
$array = array();
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$sql = "SELECT name,lon,lat FROM greatman";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($data=$result->fetch_assoc()){
        $array[] = $data;
    }
    echo JSON($array);
} else {
    echo null;
}
$conn->close();
?>