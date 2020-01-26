<?php
      require("sql_config.php");
      $conn=mysqli_connect($mysql_server_name, $mysql_username,'') or die("error connecting");
      mysqli_query($conn,"set names 'utf8'");
      mysqli_select_db($conn,$mysql_database);

      //Great man type data
      
      $data="";
      $array= array();


      $a=mysqli_query($conn,"SELECT occu,count(*) FROM greatman GROUP BY occu");
      class User{
        public $name;
        public $value;
      }     

      while($row = mysqli_fetch_array($a,MYSQLI_ASSOC)){
        $user=new User();
        $user->value=$row['count(*)'];
        $user->name=$row['occu'];
        $array[]=$user;
      }
      $data=json_encode($array);
      echo $data;
    ?>