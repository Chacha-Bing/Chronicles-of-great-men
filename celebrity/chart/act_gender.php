<?php
      require("sql_config.php");
      $conn=mysqli_connect($mysql_server_name, $mysql_username,'') or die("error connecting");
      mysqli_query($conn,"set names 'utf8'");
      mysqli_select_db($conn,$mysql_database);

      // Contemporaneous ratio of male to female
      
      $data="";
      $array= array();
      $act_age=$_GET["act_age"];
    
      class User{
        public $ratio_man_actage;
        public $ratio_women_actage;
      }
      $user=new User();
      $a=mysqli_query($conn,"SELECT count(*) FROM greatman WHERE act_age=$act_age AND gender='男'");
      $b=mysqli_query($conn,"SELECT count(*) FROM greatman WHERE act_age=$act_age AND gender='女'");
      
      $c = mysqli_fetch_array($a,MYSQLI_ASSOC);
      $user->ratio_man_actage=$c['count(*)'];

      $c = mysqli_fetch_array($b,MYSQLI_ASSOC);
      $user->ratio_women_actage=$c['count(*)'];

      $array[]=$user;
      $data=json_encode($array);
      echo $data;
    ?>