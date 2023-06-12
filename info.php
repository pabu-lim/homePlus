<?php 
  $con=mysqli_connect("localhost", "cookUser", "1234") or die("MySQL 접속 실패");
  phpinfo();
  mysqli_close($con);
?>
