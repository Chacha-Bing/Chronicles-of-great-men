<?php
require("./sql_config.php");
require("./Re_josn.php");

// Returns information about the works of selected great men
$array = array();
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
if(!isset($_GET["name"])) return null;
$name=$_GET["name"];

$sql = "SELECT w_name,w_type,w_time,w_pict FROM masterwork WHERE name = $name";
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